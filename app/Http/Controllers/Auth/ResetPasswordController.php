<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token, 
            'email' => $request->email,
            'guard' => $this->getGuard()
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Determine the guard based on the current route
        $guard = $this->getGuard();
        
        // Set the password broker based on the guard
        $broker = $guard === 'admin' ? 'admins' : 'organizations';
        
        // Set the redirect route based on the guard
        $redirectRoute = $guard === 'admin' ? 'admin.login' : 'org.login';

        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route($redirectRoute)->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
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
