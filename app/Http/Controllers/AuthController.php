<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang di Dashboard Admin!');
            }

            $previous = url()->previous();
            $fallback = ($previous && $previous !== url('/login')) ? $previous : route('home');

            return redirect()->intended($fallback)->with('success', 'Berhasil login!');
        }

        return back()
            ->withErrors(['login_error' => 'Email atau password salah'])
            ->withInput([
                'email' => $request->input('email'),
                '_form' => 'login',
            ])
            ->with('open_modal', 'login');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $profile = Profile::create([
                'umur' => 0,
                'bio' => '-',
                'alamat' => '-',
            ]);

            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'profile_id' => $profile->id,
                'role_id' => 1,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Registrasi berhasil! Selamat datang.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Berhasil logout.');
    }
}

