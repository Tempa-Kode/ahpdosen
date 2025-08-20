<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function prosesLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'username atau passsword salah');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function dashboard()
    {
        // Data dummy untuk dosen terbaik
        $dosenTerbaik = [
            [
                'nama' => 'Dosen A',
                'prodi' => 'Teknik Informatika',
                'skor' => 92.5
            ],
            [
                'nama' => 'Dosen B',
                'prodi' => 'Sistem Informasi',
                'skor' => 89.8
            ],
            [
                'nama' => 'Dosen C',
                'prodi' => 'Teknik Informatika',
                'skor' => 87.3
            ],
            [
                'nama' => 'Dosen D',
                'prodi' => 'Sistem Informasi',
                'skor' => 85.6
            ],
            [
                'nama' => 'Dosen E',
                'prodi' => 'Sains Data',
                'skor' => 84.2
            ],
            [
                'nama' => 'Dosen F',
                'prodi' => 'Teknik Informatika',
                'skor' => 82.9
            ],
            [
                'nama' => 'Dosen G',
                'prodi' => 'Sistem Informasi',
                'skor' => 81.4
            ],
            [
                'nama' => 'Dosen H',
                'prodi' => 'Sains Data',
                'skor' => 80.7
            ]
        ];

        return view('dashboard', compact('dosenTerbaik'));
    }
}
