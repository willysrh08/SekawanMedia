<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Pengguna;

class DashboardController extends Controller
{
    public function index()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        $currentUser = Pengguna::find($userId);
        $userEmail = strtolower($currentUser?->email ?? session('user_email', ''));
        $userPeran = strtolower($currentUser?->peran ?? session('user_peran', 'pegawai'));

        if (str_contains($userEmail, 'manajer')) {
            $userPeran = 'manajer';
        } elseif (str_contains($userEmail, 'atasan')) {
            $userPeran = 'atasan';
        } elseif (str_contains($userEmail, 'admin')) {
            $userPeran = 'admin';
        } elseif (str_contains($userEmail, 'pegawai')) {
            $userPeran = 'pegawai';
        }

        $pendingList = Pemesanan::where('status', 'tertunda')->get();

        return view('dashboard.index', compact(
            'currentUser',
            'userPeran',
            'pendingList'
        ));
    }
}
