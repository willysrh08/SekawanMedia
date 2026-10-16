<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login Manual (Menyesuaikan tabel pengguna custom kita)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari pengguna berdasarkan email di tabel 'pengguna'
        $user = DB::table('pengguna')->where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            $email = strtolower($user->email);
            $peran = strtolower($user->peran);

            // Sesuaikan peran dengan email login
            if (str_contains($email, 'manajer')) {
                $peran = 'manajer';
            } elseif (str_contains($email, 'atasan')) {
                $peran = 'atasan';
            } elseif (str_contains($email, 'admin')) {
                $peran = 'admin';
            } elseif (str_contains($email, 'pegawai')) {
                $peran = 'pegawai';
            }

            // Sinkronkan peran ke database jika ada perbedaan
            if ($peran !== strtolower($user->peran)) {
                DB::table('pengguna')->where('id', $user->id)->update(['peran' => $peran]);
            }

            // Simpan data user ke dalam session manual
            session([
                'user_id' => $user->id,
                'user_nama' => $user->nama,
                'user_email' => $user->email,
                'user_peran' => $peran,
                'wilayah_id' => $user->wilayah_id,
            ]);

            return redirect('/')->with('success', 'Berhasil masuk sebagai '.ucfirst($peran));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    // Proses Logout
    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect('/login')->with('success', 'Anda telah keluar.');
    }
}
