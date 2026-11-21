<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email', [
            'guard' => $this->getGuard()
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Determine the guard based on the current route
        $guard = $this->getGuard();
        
        // Set the password broker based on the guard
        $broker = $guard === 'admin' ? 'admins' : 'organizations';
        
        Log::info('Password reset attempt', [
            'email' => $request->email,
            'guard' => $guard,
            'broker' => $broker
        ]);
        
        // Set the redirect route based on the guard
        $redirectRoute = $guard . '.password.request';

        $status = Password::broker($broker)->sendResetLink(
            $request->only('email'),
            function ($user, $token) use ($guard) {
                Log::info('Sending password reset notification', [
                    'user_email' => $user->email,
                    'token' => $token,
                    'guard' => $guard
                ]);
                $user->sendPasswordResetNotification($token);
            }
        );

        Log::info('Password reset status', ['status' => $status]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
    
    /**
     * Get the guard based on the current route
     *
     * @return string
     */
    protected function getGuard()
    {
        $route = request()->route();
        if (str_contains($route->getName(), 'admin.')) {
            return 'admin';
        }
        return 'organization';
    }
}
