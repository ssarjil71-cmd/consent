<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class Msg91DLTService
{
    protected $authKey;
    protected $senderId;
    protected $country;

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->senderId = config('services.msg91.sender_id');
        $this->country = config('services.msg91.country', '91');
    }

    /**
     * Send welcome message with login credentials using DLT template
     */
    public function sendWelcomeMessage($mobile, $email, $password, $loginLink)
    {
        try {
            if (empty($mobile)) {
                Log::info('No mobile number provided for welcome message');
                return false;
            }

            $templateId = trim(config('services.msg91.template_id') ?? env('MSG91_DLT_WELCOME_TEMPLATE_ID', ''));
            $templateIdLength = strlen($templateId);
            $templateIdentifierType = ctype_digit($templateId) ? 'numeric_template_id' : 'alphanumeric_flow_id';

            // Validate template/flow identifier format (accept both numeric DLT IDs and alphanumeric Flow IDs)
            if (empty($templateId) || $templateIdLength < 10 || !preg_match('/^[A-Za-z0-9_-]+$/', $templateId)) {
                Log::error('Invalid MSG91 template identifier provided.', [
                    'template_id' => $templateId,
                    'length' => $templateIdLength,
                    'identifier_type' => $templateIdentifierType
                ]);
                return false;
            }
            
            Log::info('MSG91 DLT Configuration Check:', [
                'auth_key_set' => !empty($this->authKey),
                'auth_key_length' => strlen($this->authKey),
                'template_id' => $templateId,
                'template_id_length' => $templateIdLength,
                'mobile' => $mobile,
                'email' => $email,
                'identifier_type' => $templateIdentifierType
            ]);
            
            if (empty($this->authKey)) {
                Log::warning('MSG91 API key not configured');
                return false;
            }

            // DLT Template API
            $url = "https://api.msg91.com/api/v5/flow";
            
            $data = [
                "template_id" => $templateId, // DLT template ID or Flow ID for login credentials
                "short_url" => "0",
                "recipients" => [
                    [
                        "mobiles" => $this->country . $mobile,
                        "var1" => $email,      // ##var1## = Email
                        "var2" => $password,   // ##var2## = Password
                        "var3" => $loginLink,  // ##var3## = Login Link
                    ]
                ]
            ];

            $headers = [
                "authkey: " . $this->authKey,
                "Content-Type: application/json"
            ];

            Log::debug('MSG91 API Request:', [
                'endpoint' => $url,
                'payload' => $data,
                'headers' => $headers,
                'template_id' => $templateId,
                'mobile' => $mobile
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            fclose($verbose);

            curl_close($ch);

            Log::debug('MSG91 API Response:', [
                'http_code' => $httpCode,
                'response' => $response,
                'error' => $error,
                'verbose_output' => $verboseLog,
                'decoded_response' => json_decode($response, true)
            ]);

            $result = [
                'status' => $httpCode == 200,
                'response' => json_decode($response, true),
                'error' => $error,
                'http_code' => $httpCode
            ];

            if (!$result['status']) {
                Log::error('MSG91 API Failed', [
                    'mobile' => $mobile,
                    'template_id' => $templateId,
                    'error' => $error,
                    'http_code' => $httpCode,
                    'api_response' => $result['response'],
                    'request_payload' => $data
                ]);
                return false;
            }

            $success = isset($result['response']['type']) && $result['response']['type'] === 'success';

            if ($success) {
                $messageHex = $result['response']['message'] ?? null;
                $decodedMessageId = null;

                if (is_string($messageHex) && strlen($messageHex) % 2 === 0 && ctype_xdigit($messageHex)) {
                    $decodedMessageId = hex2bin($messageHex);
                }

                Log::info('DLT SMS accepted by MSG91.', [
                    'mobile' => $mobile,
                    'template_id' => $templateId,
                    'identifier_type' => $templateIdentifierType,
                    'message_id_hex' => $messageHex,
                    'message_id' => $decodedMessageId,
                ]);
                return true;
            } else {
                Log::warning("MSG91 DLT API returned non-success response for: {$mobile}, response: " . json_encode($result['response']));
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Failed to send DLT welcome SMS: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test template ID validity
     */
    public function testTemplate($templateId)
    {
        try {
            if (empty($this->authKey)) {
                return ['error' => 'MSG91 API key not configured'];
            }

            $url = "https://api.msg91.com/api/v5/flow";
            
            $data = [
                "template_id" => $templateId,
                "short_url" => "0",
                "recipients" => [
                    [
                        "mobiles" => $this->country . "9999999999", // Test mobile
                        "var1" => "test@example.com",
                        "var2" => "test123",
                        "var3" => "http://test.com",
                    ]
                ]
            ];

            $headers = [
                "authkey: " . $this->authKey,
                "Content-Type: application/json"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'http_code' => $httpCode,
                'response' => json_decode($response, true),
                'error' => $error,
                'template_id' => $templateId
            ];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
