<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\PemeliharaanKendaraan;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource (Read All).
     */
    public function index()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        $currentUser = Pengguna::find($userId);
        $userPeran = strtolower($currentUser?->peran ?? session('user_peran', 'pegawai'));
        $pendingList = Pemesanan::where('status', 'tertunda')->get();
        $jadwals = Jadwal::latest()->get();

        return view('jadwal.index', compact('jadwals', 'pendingList', 'userPeran', 'currentUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        return view('jadwal.create');
    }

    /**
     * Store a newly created resource in storage (Create).
     */
    public function store(Request $request)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|in:driver,servis',
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'penanggung_jawab' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $jadwal = Jadwal::create($request->all());

        // Sinkronisasi otomatis ke pemeliharaan_kendaraan jika kategori adalah servis
        if ($jadwal->kategori === 'servis') {
            $kendaraan = Kendaraan::where('nama', 'like', "%{$request->judul}%")
                ->orWhere('plat_nomor', 'like', "%{$request->judul}%")
                ->first() ?? Kendaraan::first();

            if ($kendaraan) {
                PemeliharaanKendaraan::create([
                    'kendaraan_id' => $kendaraan->id,
                    'tanggal_servis' => $request->tanggal,
                    'kilometer_servis' => 15000,
                    'deskripsi' => $request->keterangan ?? "Servis berkala unit {$jadwal->judul} oleh {$request->penanggung_jawab}",
                    'biaya' => 750000.00,
                ]);
            }
        }

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Tambah Jadwal',
                'deskripsi' => "Menambahkan jadwal {$request->kategori}: {$request->judul} ({$request->penanggung_jawab})",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Jadwal berhasil ditambahkan!', 'data' => $jadwal], 201);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Display the specified resource (Read One).
     */
    public function show(Jadwal $jadwal)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['data' => $jadwal]);
        }

        return view('jadwal.show', compact('jadwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jadwal $jadwal)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        return view('jadwal.edit', compact('jadwal'));
    }

    /**
     * Update the specified resource in storage (Update).
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|in:driver,servis',
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'penanggung_jawab' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $jadwal->update($request->all());

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Update Jadwal',
                'deskripsi' => "Memperbarui jadwal #{$jadwal->id}: {$request->judul}",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Jadwal berhasil diperbarui!', 'data' => $jadwal]);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage (Delete).
     */
    public function destroy(Jadwal $jadwal)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $judul = $jadwal->judul;
        $id = $jadwal->id;
        $jadwal->delete();

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Hapus Jadwal',
                'deskripsi' => "Menghapus jadwal #{$id}: {$judul}",
            ]);
        }

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Jadwal berhasil dihapus!']);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}
