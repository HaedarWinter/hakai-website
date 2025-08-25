<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login', [
            'title' => 'Masuk',
        ]);
    }

    public function loginProses(Request $request)
    {
        // Validasi Input
            $credentials = $request->validate([
                'email' => ['required', 'email'], // tambahin validasi email
                'password' => ['required',  'string', 'min:8', 'max:16'],
            ], [
                'email.required' => 'Email Tidak Boleh Kosong',
                'email.email' => 'Email Tidak Valid',
                'password.required' => 'Kata Sandi Tidak Boleh Kosong',
                'password.min' => 'Kata Sandi Minimal 8 Karakter',
                'password.max' => 'Kata Sandi Maksimal 16 Karakter',
            ]);
            //coba login
        if (Auth::attempt($credentials, $request-> filled('remember'))) {
            //Regenerate session biar lebih aman (hindari session fixation)
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Selamat Datang Kembali!');
        }

        //kalau gagal
        return back()->withErrors([
            'email' => 'Email atau Password Salah',
        ])->onlyInput('email'); // Biar password ga ikut ke-repopulate

    }

        //Proses logout user

        public function logout(Request $request)
        {
            Auth::logout();
            // Invalidate Seluruh Session
            $request->session()->invalidate();
            // Regenerate CSRF Token
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Anda Berhasil Logout');
        }
    }
