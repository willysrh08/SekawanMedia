<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    /**
     * Halaman Manajemen Armada Tambang (Read All)
     */
    public function index()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        $currentUser = Pengguna::find($userId);
        $userPeran = strtolower($currentUser?->peran ?? session('user_peran', 'admin'));
        $pendingList = Pemesanan::where('status', 'tertunda')->get();
        $armadaList = Kendaraan::latest()->get();

        return view('armada.index', compact('armadaList', 'pendingList', 'userPeran', 'currentUser'));
    }

    /**
     * Simpan Unit Kendaraan Baru (Create)
     */
    public function store(Request $request)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $request->validate([
            'merek' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'plat_nomor' => 'required|string|max:50|unique:kendaraan,plat_nomor',
            'status' => 'required|in:tersedia,digunakan,maintenance',
            'jenis' => 'nullable|in:penumpang,barang',
            'kepemilikan' => 'nullable|in:milik_perusahaan,sewa',
            'kapasitas' => 'nullable|numeric|min:1',
        ], [
            'plat_nomor.unique' => 'Pelat nomor sudah terdaftar di sistem.',
        ]);

        $wilayahId = session('wilayah_id') ?? Wilayah::first()?->id ?? 1;

        // Klasifikasi otomatis jika tidak ditentukan
        $jenis = $request->jenis;
        if (! $jenis) {
            $modelStr = strtolower($request->model);
            $merekStr = strtolower($request->merek);
            $jenis = 'penumpang';
            if (str_contains($modelStr, 'truk') || str_contains($modelStr, 'dump') || str_contains($merekStr, 'fuso') || str_contains($modelStr, 'hino')) {
                $jenis = 'barang';
            }
        }

        $kepemilikan = $request->kepemilikan ?? 'milik_perusahaan';
        $kapasitas = $request->kapasitas ?? ($jenis === 'barang' ? 2 : 4);

        // Simpan unit kendaraan ke database
        $kendaraan = Kendaraan::create([
            'wilayah_id' => $wilayahId,
            'nama' => trim($request->merek.' '.$request->model),
            'merek' => $request->merek,
            'model' => $request->model,
            'plat_nomor' => strtoupper(trim($request->plat_nomor)),
            'jenis' => $jenis,
            'kepemilikan' => $kepemilikan,
            'kapasitas' => $kapasitas,
            'status' => $request->status,
        ]);

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Tambah Unit Armada',
                'deskripsi' => "Menambahkan unit armada baru: {$kendaraan->nama} ({$kendaraan->plat_nomor}) status {$request->status}",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Armada berhasil ditambahkan', 'data' => $kendaraan], 201);
        }

        return redirect()->back()->with('success', 'Mobil telah ditambahkan.');
    }

    /**
     * Tampilkan Detail Unit Kendaraan (Read One)
     */
    public function show($id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $kendaraan = Kendaraan::with('wilayah')->findOrFail($id);

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['data' => $kendaraan]);
        }

        return response()->json(['data' => $kendaraan]);
    }

    /**
     * Update Unit Kendaraan (Update)
     */
    public function update(Request $request, $id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $kendaraan = Kendaraan::findOrFail($id);

        $request->validate([
            'merek' => 'sometimes|required|string|max:100',
            'model' => 'sometimes|required|string|max:100',
            'plat_nomor' => 'sometimes|required|string|max:50|unique:kendaraan,plat_nomor,'.$id,
            'status' => 'sometimes|required|in:tersedia,digunakan,maintenance',
            'jenis' => 'nullable|in:penumpang,barang',
            'kepemilikan' => 'nullable|in:milik_perusahaan,sewa',
            'kapasitas' => 'nullable|numeric|min:1',
        ]);

        $data = $request->only(['merek', 'model', 'plat_nomor', 'jenis', 'kepemilikan', 'kapasitas', 'status']);

        if ($request->filled('merek') || $request->filled('model')) {
            $merek = $request->merek ?? $kendaraan->merek;
            $model = $request->model ?? $kendaraan->model;
            $data['nama'] = trim($merek.' '.$model);
        }

        if ($request->filled('plat_nomor')) {
            $data['plat_nomor'] = strtoupper(trim($request->plat_nomor));
        }

        $kendaraan->update($data);

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Update Unit Armada',
                'deskripsi' => "Memperbarui unit armada #{$kendaraan->id}: {$kendaraan->nama} ({$kendaraan->plat_nomor})",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Data armada berhasil diperbarui', 'data' => $kendaraan]);
        }

        return redirect()->back()->with('success', 'Data armada berhasil diperbarui.');
    }

    /**
     * Hapus Unit Kendaraan (Delete)
     */
    public function destroy($id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $kendaraan = Kendaraan::findOrFail($id);

        // Proteksi: jangan hapus jika sedang digunakan
        if ($kendaraan->status === 'digunakan') {
            return redirect()->back()->with('error', 'Armada sedang berstatus digunakan dan tidak dapat dihapus.');
        }

        $nama = $kendaraan->nama;
        $plat = $kendaraan->plat_nomor;
        $kendaraan->delete();

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Hapus Unit Armada',
                'deskripsi' => "Menghapus unit armada: {$nama} ({$plat})",
            ]);
        }

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Armada berhasil dihapus.']);
        }

        return redirect()->back()->with('success', 'Armada berhasil dihapus.');
    }
}
