<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\PersetujuanPemesanan;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    /**
     * Resolusi peran pengguna berdasarkan data akun dan session secara konsisten.
     */
    private function resolvePeran(?Pengguna $user, ?string $sessionEmail = null, ?string $sessionPeran = null): string
    {
        $email = strtolower($user?->email ?? $sessionEmail ?? '');
        $peran = strtolower($user?->peran ?? $sessionPeran ?? 'pegawai');

        if (str_contains($email, 'manajer')) {
            return 'manajer';
        } elseif (str_contains($email, 'atasan')) {
            return 'atasan';
        } elseif (str_contains($email, 'admin')) {
            return 'admin';
        } elseif (str_contains($email, 'pegawai')) {
            return 'pegawai';
        }

        return $peran;
    }

    /**
     * Halaman Utama Manajemen Pemesanan & Persetujuan
     */
    public function index()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        $currentUser = Pengguna::find($userId);
        $userPeran = $this->resolvePeran($currentUser, session('user_email'), session('user_peran'));

        $isManajer = ($userPeran === 'manajer');
        $isAdmin = ($userPeran === 'admin');
        $isAtasan = ($userPeran === 'atasan');
        // Hanya Atasan dan Manajer yang berwenang memberikan approval, namun flag isApprover tetap dijaga untuk kesesuaian data tab
        $isApprover = ($isAtasan || $isManajer || $isAdmin);

        // Ambil daftar kendaraan & driver yang tersedia
        $kendaraanList = Kendaraan::all();
        $driverList = Driver::all();

        // Ambil semua daftar pemesanan
        $pemesananList = Pemesanan::with(['pegawai', 'kendaraan', 'driver', 'persetujuan.approver'])
            ->latest()
            ->get();

        // Ambil pemesanan yang statusnya tertunda (menunggu persetujuan)
        $pendingList = Pemesanan::with(['pegawai', 'kendaraan', 'driver', 'persetujuan.approver'])
            ->where('status', 'tertunda')
            ->latest()
            ->get();

        $riwayatList = $pemesananList;

        return view('pemesanan.index', compact(
            'currentUser',
            'userPeran',
            'isManajer',
            'isAdmin',
            'isAtasan',
            'isApprover',
            'kendaraanList',
            'driverList',
            'pemesananList',
            'pendingList',
            'riwayatList'
        ));
    }

    /**
     * 1. Pengajuan Pemesanan Baru (Pegawai / Admin)
     * Admin bertugas input pemesanan, assign driver & approver (Atasan L1 & Manajer L2).
     * Admin TIDAK menjadi approver.
     */
    public function ajukan(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pengguna,id',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'keperluan' => 'required|string',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',
            'driver_id' => 'nullable|exists:driver,id',
            'lokasi_asal' => 'nullable|string',
            'tujuan' => 'nullable|string',
            'approver_1_id' => 'nullable|exists:pengguna,id',
            'approver_2_id' => 'nullable|exists:pengguna,id',
        ]);

        // Simpan data pemesanan utama dengan status awal 'tertunda'
        $pemesanan = Pemesanan::create([
            'pegawai_id' => $request->pegawai_id,
            'kendaraan_id' => $request->kendaraan_id ?? null,
            'driver_id' => $request->driver_id ?? null,
            'lokasi_asal' => $request->lokasi_asal ?? 'Site 01 - Pomalaa (Office Utama)',
            'tujuan' => $request->tujuan ?? 'Pit West Area (Tambang Nikel)',
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keperluan' => $request->keperluan,
            'status' => 'tertunda',
        ]);

        $pemesan = Pengguna::find($request->pegawai_id);

        // 1. Tentukan Approver Level 1 (Atasan Langsung)
        $atasanId = $request->input('approver_1_id');
        if (! $atasanId) {
            if ($pemesan && $pemesan->atasan_id) {
                $atasanId = $pemesan->atasan_id;
            } else {
                $atasan = Pengguna::where('peran', 'atasan')->first();
                $atasanId = $atasan ? $atasan->id : null;
            }
        }

        // 2. Tentukan Approver Level 2 (Manajer / Kepala Wilayah)
        $manajerId = $request->input('approver_2_id') ?? $request->input('approver_id');
        if (! $manajerId) {
            $atasanUser = $atasanId ? Pengguna::find($atasanId) : null;
            if ($atasanUser && $atasanUser->atasan_id) {
                $manajerId = $atasanUser->atasan_id;
            } else {
                $manajer = Pengguna::where('peran', 'manajer')->first();
                $manajerId = $manajer ? $manajer->id : null;
            }
        }

        // Fallback jika belum terisi di sistem
        if (! $atasanId) {
            $atasanId = Pengguna::where('peran', 'atasan')->first()?->id
                ?? Pengguna::where('peran', 'manajer')->first()?->id;
        }
        if (! $manajerId) {
            $manajerId = Pengguna::where('peran', 'manajer')->first()?->id
                ?? Pengguna::where('peran', 'atasan')->latest()->first()?->id;
        }

        // Proteksi Hak Akses: Admin TIDAK BOLEH menjadi pihak penyetuju (approver)
        $adminIds = Pengguna::where('peran', 'admin')->pluck('id')->toArray();
        if (in_array($atasanId, $adminIds)) {
            $atasanId = Pengguna::where('peran', 'atasan')->first()?->id ?? Pengguna::where('peran', 'manajer')->first()?->id;
        }
        if (in_array($manajerId, $adminIds)) {
            $manajerId = Pengguna::where('peran', 'manajer')->first()?->id ?? Pengguna::where('peran', 'atasan')->latest()->first()?->id;
        }

        // TAHAP 1: Otorisasi Atasan Langsung (Level 1)
        PersetujuanPemesanan::create([
            'pemesanan_id' => $pemesanan->id,
            'approver_id' => $atasanId,
            'level' => 1,
            'status' => 'tertunda',
            'catatan' => null,
        ]);

        // TAHAP 2: Otorisasi Manajer (Level 2)
        PersetujuanPemesanan::create([
            'pemesanan_id' => $pemesanan->id,
            'approver_id' => $manajerId,
            'level' => 2,
            'status' => 'tertunda',
            'catatan' => null,
        ]);

        // Catat ke Log Aktivitas
        $currentUserId = session('user_id') ?? $request->pegawai_id;
        if ($currentUserId && Pengguna::where('id', $currentUserId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $currentUserId,
                'aksi' => 'Pengajuan Pemesanan Kendaraan',
                'deskripsi' => "Mengajukan pemesanan #REQ-{$pemesanan->id} untuk keperluan: {$request->keperluan}",
            ]);
        }

        return redirect()->back()->with('success', 'Menunggu persetujuan');
    }

    /**
     * 2. Proses Approval (Persetujuan / Penolakan)
     * - Admin TIDAK BOLEH melakukan persetujuan. Jika request datang dari admin, sistem menolak.
     * - Approval HANYA dilakukan oleh Atasan (Level 1) dan Manajer (Level 2).
     */
    public function prosesApproval(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',
            'driver_id' => 'nullable|exists:driver,id',
        ]);

        // Prioritaskan Pemesanan ID terlebih dahulu karena formulir Blade & Unit Test mengirimkan Pemesanan ID
        $pemesanan = Pemesanan::find($id);
        $persetujuan = null;
        if (! $pemesanan) {
            $persetujuan = PersetujuanPemesanan::find($id);
            $pemesanan = $persetujuan ? $persetujuan->pemesanan : null;
        }

        if (! $pemesanan) {
            return redirect()->back()->with('error', 'Data pemesanan tidak ditemukan.');
        }

        $userId = session('user_id');
        $currentUser = Pengguna::find($userId);
        $userPeran = $this->resolvePeran($currentUser, session('user_email'), session('user_peran'));

        $isManajer = ($userPeran === 'manajer');
        $isAdmin = ($userPeran === 'admin');
        $isAtasan = ($userPeran === 'atasan');

        $level1 = PersetujuanPemesanan::where('pemesanan_id', $pemesanan->id)->where('level', 1)->first();
        $level2 = PersetujuanPemesanan::where('pemesanan_id', $pemesanan->id)->where('level', 2)->first();

        $logUserId = ($userId && Pengguna::where('id', $userId)->exists())
            ? $userId
            : ($pemesanan->pegawai_id && Pengguna::where('id', $pemesanan->pegawai_id)->exists() ? $pemesanan->pegawai_id : null);

        // ==============================================================
        // 1. ATURAN HAK AKSES ADMIN (STRICT BACKEND ENFORCEMENT)
        // Admin TIDAK BOLEH melakukan persetujuan (approval/rejection) pemesanan.
        // Tugas admin murni: Input pemesanan, assign driver & kendaraan, serta assign approver.
        // ==============================================================
        if ($isAdmin) {
            // Jika admin memilih armada atau driver, simpan penugasan tersebut
            $assignUpdated = false;
            if ($request->filled('kendaraan_id')) {
                $pemesanan->kendaraan_id = $request->kendaraan_id;
                $assignUpdated = true;
            }
            if ($request->filled('driver_id')) {
                $pemesanan->driver_id = $request->driver_id;
                $assignUpdated = true;
            }
            if ($assignUpdated) {
                $pemesanan->save();
            }

            if ($logUserId) {
                LogAktivitas::create([
                    'pengguna_id' => $logUserId,
                    'aksi' => 'Penolakan Akses Approval (Admin)',
                    'deskripsi' => "Aksi persetujuan untuk pemesanan #REQ-{$pemesanan->id} oleh Admin ditolak karena hak approval hanya dimiliki oleh Atasan (Level 1) dan Manajer (Level 2).",
                ]);
            }

            return redirect()->back()->with(
                'error',
                'Admin tidak memiliki hak akses untuk menyetujui atau menolak pemesanan. Approval hanya boleh dilakukan oleh Atasan (Level 1) dan Manajer (Level 2).'
            );
        }

        // ==============================================================
        // 2. OTORISASI PERAN BUKAN ATASAN ATAU MANAJER (PEGAWAI DLL)
        // ==============================================================
        if (! $isAtasan && ! $isManajer) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk melakukan persetujuan pemesanan.');
        }

        // ==============================================================
        // 3. PROSES PENOLAKAN (DITOLAK)
        // ==============================================================
        if ($request->status === 'ditolak') {
            if ($isManajer) {
                if ($level2) {
                    $level2->update([
                        'status' => 'ditolak',
                        'approver_id' => $userId ?? $level2->approver_id,
                        'catatan' => $request->catatan ?? 'Pemesanan ditolak oleh Manajer (Level 2)',
                    ]);
                }
                if ($level1 && $level1->status === 'tertunda') {
                    $level1->update([
                        'status' => 'ditolak',
                        'catatan' => 'Dibatalkan karena ditolak langsung oleh Manajer (Level 2)',
                    ]);
                }
            } elseif ($isAtasan) {
                if ($level1) {
                    $level1->update([
                        'status' => 'ditolak',
                        'approver_id' => $userId ?? $level1->approver_id,
                        'catatan' => $request->catatan ?? 'Pemesanan ditolak oleh Atasan (Level 1)',
                    ]);
                }
                if ($level2 && $level2->status === 'tertunda') {
                    $level2->update([
                        'status' => 'ditolak',
                        'catatan' => 'Dibatalkan karena ditolak oleh Atasan pada Tahap 1',
                    ]);
                }
            }

            $pemesanan->update(['status' => 'ditolak']);

            if ($logUserId) {
                LogAktivitas::create([
                    'pengguna_id' => $logUserId,
                    'aksi' => 'Penolakan Pemesanan',
                    'deskripsi' => "Pemesanan #REQ-{$pemesanan->id} ditolak oleh ".ucfirst($userPeran).($request->catatan ? ": {$request->catatan}" : ''),
                ]);
            }

            return redirect()->back()->with('success', 'Pemesanan ditolak');
        }

        // ==============================================================
        // 4. PROSES PERSETUJUAN (DISETUJUI)
        // ==============================================================

        // KASUS A: DISETUJUI OLEH ATASAN (LEVEL 1)
        if ($isAtasan) {
            // Update Level 1 (Atasan) menjadi disetujui
            if ($level1) {
                $level1->update([
                    'status' => 'disetujui',
                    'approver_id' => $userId ?? $level1->approver_id,
                    'catatan' => $request->catatan ?? 'Disetujui oleh Atasan (Tahap 1 Selesai)',
                ]);
            }

            // Simpan armada & driver jika dipilih
            if ($request->filled('kendaraan_id')) {
                $pemesanan->kendaraan_id = $request->kendaraan_id;
            }
            if ($request->filled('driver_id')) {
                $pemesanan->driver_id = $request->driver_id;
            }
            $pemesanan->save();

            if ($logUserId) {
                LogAktivitas::create([
                    'pengguna_id' => $logUserId,
                    'aksi' => 'Persetujuan Tahap 1 (Atasan)',
                    'deskripsi' => "Pemesanan #REQ-{$pemesanan->id} disetujui Tahap 1 oleh Atasan Langsung",
                ]);
            }

            // Jika Level 2 (Manajer) sebelumnya sudah disetujui, maka pemesanan komplit disetujui
            if ($level2 && $level2->status === 'disetujui') {
                $pemesanan->update(['status' => 'disetujui']);
                if ($pemesanan->kendaraan_id) {
                    Kendaraan::where('id', $pemesanan->kendaraan_id)->update(['status' => 'digunakan']);
                }

                return redirect()->back()->with('success', 'Pemesanan disetujui');
            }

            return redirect()->back()->with('success', 'Pemesanan disetujui');
        }

        // KASUS B: DISETUJUI OLEH MANAJER (LEVEL 2 - OTORISASI TERTINGGI & BYPASS)
        if ($isManajer) {
            // Update Level 2 (Manajer) menjadi disetujui
            if ($level2) {
                $level2->update([
                    'status' => 'disetujui',
                    'approver_id' => $userId ?? $level2->approver_id,
                    'catatan' => $request->catatan ?? 'Disetujui oleh Manajer (Level 2)',
                ]);
            }

            // Jika Tahap 1 (Atasan) masih tertunda, otomatis dilewati (karena otorisasi tertinggi Manajer)
            if ($level1 && $level1->status === 'tertunda') {
                $level1->update([
                    'status' => 'dilewati',
                    'catatan' => 'Dilewati secara otomatis karena telah disetujui langsung oleh Manajer',
                ]);
            }

            // Simpan armada & driver jika dipilih
            if ($request->filled('kendaraan_id')) {
                $pemesanan->kendaraan_id = $request->kendaraan_id;
            }
            if ($request->filled('driver_id')) {
                $pemesanan->driver_id = $request->driver_id;
            }

            // Status pemesanan final langsung DISETUJUI
            $pemesanan->update(['status' => 'disetujui']);

            if ($pemesanan->kendaraan_id) {
                Kendaraan::where('id', $pemesanan->kendaraan_id)->update(['status' => 'digunakan']);
            }

            if ($logUserId) {
                LogAktivitas::create([
                    'pengguna_id' => $logUserId,
                    'aksi' => 'Persetujuan Pemesanan (Manajer)',
                    'deskripsi' => "Pemesanan #REQ-{$pemesanan->id} disetujui oleh Manajer",
                ]);
            }

            return redirect()->back()->with('success', 'Pemesanan disetujui');
        }

        return redirect()->back()->with('error', 'Status persetujuan tidak dapat diproses.');
    }

    /**
     * 3. Admin Menetapkan Kendaraan, Driver, dan Approver (Pihak Penyetuju)
     */
    public function assignAdmin(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id' => 'nullable|exists:kendaraan,id',
            'driver_id' => 'nullable|exists:driver,id',
            'approver_1_id' => 'nullable|exists:pengguna,id',
            'approver_2_id' => 'nullable|exists:pengguna,id',
        ]);

        $pemesanan = Pemesanan::findOrFail($id);

        $updateData = [];
        if ($request->has('kendaraan_id')) {
            $updateData['kendaraan_id'] = $request->kendaraan_id;
        }
        if ($request->has('driver_id')) {
            $updateData['driver_id'] = $request->driver_id;
        }
        if (! empty($updateData)) {
            $pemesanan->update($updateData);
        }

        // Admin menetapkan approver (pihak penyetuju) jika ditentukan
        if ($request->filled('approver_1_id')) {
            PersetujuanPemesanan::updateOrCreate(
                ['pemesanan_id' => $pemesanan->id, 'level' => 1],
                ['approver_id' => $request->approver_1_id]
            );
        }
        if ($request->filled('approver_2_id')) {
            PersetujuanPemesanan::updateOrCreate(
                ['pemesanan_id' => $pemesanan->id, 'level' => 2],
                ['approver_id' => $request->approver_2_id]
            );
        }

        $userId = session('user_id');
        $logUserId = ($userId && Pengguna::where('id', $userId)->exists())
            ? $userId
            : ($pemesanan->pegawai_id && Pengguna::where('id', $pemesanan->pegawai_id)->exists() ? $pemesanan->pegawai_id : null);

        if ($logUserId) {
            LogAktivitas::create([
                'pengguna_id' => $logUserId,
                'aksi' => 'Penetapan Armada & Driver',
                'deskripsi' => "Admin menetapkan armada & driver untuk pemesanan #REQ-{$pemesanan->id}",
            ]);
        }

        return redirect()->back()->with('success', 'Armada dan driver berhasil ditetapkan untuk pemesanan #REQ-'.$pemesanan->id.'.');
    }
}
