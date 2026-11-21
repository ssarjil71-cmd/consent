<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'msg91' => [
        'auth_key' => env('MSG91_AUTH_KEY'),
        'sender' => env('MSG91_SENDER_ID'),
        'country' => env('MSG91_COUNTRY', '91'),
        'endpoint' => env('MSG91_OTP_ENDPOINT', 'https://api.msg91.com/api/v5/flow'),
        'flow_id' => env('MSG91_FLOW_ID', env('MSG91_DLT_WELCOME_TEMPLATE_ID')),
        'template_id' => env('MSG91_TEMPLATE_ID'),
        'otp_expiry' => env('MSG91_OTP_EXPIRY', 5),
    ],

    'ipgeolocation' => [
        'token' => env('IP_GEOLOCATION_TOKEN'),
        'endpoint' => env('IP_GEOLOCATION_ENDPOINT'),
    ],

    'surepass' => [
        'base_url' => env('SUREPASS_BASE_URL'),
        'api_key' => env('SUREPASS_API_KEY'),
        'pan_endpoint' => env('SUREPASS_PAN_ENDPOINT'),
        'voter_endpoint' => env('SUREPASS_VOTER_ENDPOINT'),
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
    ],

];
