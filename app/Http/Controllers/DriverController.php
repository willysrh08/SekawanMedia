<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\LogAktivitas;
use App\Models\Pengguna;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Tampilkan Daftar Driver (Read All)
     */
    public function index()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $drivers = Driver::latest()->get();

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['data' => $drivers]);
        }

        return response()->json(['data' => $drivers]);
    }

    /**
     * Simpan Driver Baru (Create)
     */
    public function store(Request $request)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'status' => 'required|in:tersedia,bertugas,libur',
            'wilayah_id' => 'nullable|exists:wilayah,id',
        ]);

        $wilayahId = $request->wilayah_id ?? session('wilayah_id') ?? Wilayah::first()?->id ?? 1;

        $driver = Driver::create([
            'wilayah_id' => $wilayahId,
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'status' => $request->status,
        ]);

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Tambah Driver',
                'deskripsi' => "Menambahkan driver baru: {$driver->nama} ({$driver->telepon})",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Driver berhasil ditambahkan.', 'data' => $driver], 201);
        }

        return redirect()->back()->with('success', 'Driver berhasil ditambahkan.');
    }

    /**
     * Tampilkan Detail Driver (Read One)
     */
    public function show($id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $driver = Driver::findOrFail($id);

        return response()->json(['data' => $driver]);
    }

    /**
     * Update Data Driver (Update)
     */
    public function update(Request $request, $id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $driver = Driver::findOrFail($id);

        $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'telepon' => 'sometimes|required|string|max:20',
            'status' => 'sometimes|required|in:tersedia,bertugas,libur',
        ]);

        $driver->update($request->only(['nama', 'telepon', 'status']));

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Update Driver',
                'deskripsi' => "Memperbarui driver #{$driver->id}: {$driver->nama}",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Data driver berhasil diperbarui.', 'data' => $driver]);
        }

        return redirect()->back()->with('success', 'Data driver berhasil diperbarui.');
    }

    /**
     * Hapus Driver (Delete)
     */
    public function destroy($id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $driver = Driver::findOrFail($id);

        if ($driver->status === 'bertugas') {
            return redirect()->back()->with('error', 'Driver sedang bertugas dan tidak dapat dihapus.');
        }

        $nama = $driver->nama;
        $driver->delete();

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Hapus Driver',
                'deskripsi' => "Menghapus data driver: {$nama}",
            ]);
        }

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Driver berhasil dihapus.']);
        }

        return redirect()->back()->with('success', 'Driver berhasil dihapus.');
    }
}
