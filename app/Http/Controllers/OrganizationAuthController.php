<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrganizationAuthController extends Controller
{
    public function showLogin()
    {
        return view('organization.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $organization = Organization::where('email', $data['email'])->first();

        if (!$organization) {
            return back()->withErrors(['email' => 'Invalid email or password'])->onlyInput('email');
        }

        if (!Hash::check($data['password'], $organization->password)) {
            return back()->withErrors(['email' => 'Invalid email or password'])->onlyInput('email');
        }

        Auth::guard('organization')->login($organization, $request->boolean('remember'));
        $request->session()->regenerate();
        return redirect()->route('org.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('organization')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('org.login');
    }
}
