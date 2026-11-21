<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    /**
     * Show admin profile
     */
    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Update admin profile information
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'name' => 'required|string|max:255',
        ]);

        try {
            $admin->update([
                'email' => $request->email,
                'name' => $request->name,
            ]);

            return redirect()
                ->route('admin.profile')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        try {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()
                ->route('admin.profile')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update password. Please try again.')
                ->withInput();
        }
    }
}
