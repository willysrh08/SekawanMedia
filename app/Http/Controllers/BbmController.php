<?php

namespace App\Http\Controllers;

use App\Models\KonsumsiBbm;
use App\Models\LogAktivitas;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class BbmController extends Controller
{
    /**
     * Tampilkan Daftar & Metrik Konsumsi BBM (Read All)
     */
    public function index(Request $request)
    {
        // Cek autentikasi session
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        $currentUser = Pengguna::find($userId);
        $userPeran = strtolower($currentUser?->peran ?? session('user_peran', 'staff'));
        $pendingList = Pemesanan::where('status', 'tertunda')->get();

        // Query data konsumsi BBM dengan filter pencarian & kategori
        $query = KonsumsiBbm::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_lambung', 'like', "%{$search}%")
                    ->orWhere('driver', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            $query->where('jenis_kendaraan', $request->kategori);
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Hitung Ringkasan Metrik
        $totalBbm = KonsumsiBbm::sum('jumlah_bbm');
        $totalJarak = KonsumsiBbm::sum('odometer');
        $rataEfisiensi = KonsumsiBbm::avg('efisiensi');
        $totalBiayaJt = ($totalBbm * 15000) / 1000000;

        return view('bbm.index', compact(
            'logs',
            'pendingList',
            'userPeran',
            'currentUser',
            'totalBbm',
            'totalJarak',
            'rataEfisiensi',
            'totalBiayaJt'
        ));
    }

    /**
     * Simpan Data Konsumsi BBM Baru (Create)
     */
    public function store(Request $request)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $request->validate([
            'no_lambung' => 'required|string|max:50',
            'jenis_kendaraan' => 'required|string|max:50',
            'driver' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'odometer' => 'required|numeric|min:0',
            'jumlah_bbm' => 'required|numeric|min:1',
            'tipe_bbm' => 'required|string|max:50',
            'efisiensi' => 'nullable|numeric',
            'status_efisiensi' => 'nullable|string',
        ]);

        $efisiensi = $request->filled('efisiensi')
            ? (float) $request->efisiensi
            : ($request->jumlah_bbm > 0 ? round($request->odometer / $request->jumlah_bbm, 2) : 0);

        $statusEfisiensi = $request->filled('status_efisiensi')
            ? $request->status_efisiensi
            : ($efisiensi >= 10 ? 'Efisien' : ($efisiensi >= 6 ? 'Normal' : 'Boros'));

        $logBbm = KonsumsiBbm::create([
            'no_lambung' => strtoupper(trim($request->no_lambung)),
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'driver' => $request->driver,
            'tanggal' => $request->tanggal,
            'odometer' => $request->odometer,
            'jumlah_bbm' => $request->jumlah_bbm,
            'tipe_bbm' => $request->tipe_bbm,
            'efisiensi' => $efisiensi,
            'status_efisiensi' => $statusEfisiensi,
        ]);

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Catat Konsumsi BBM',
                'deskripsi' => "Pencatatan konsumsi BBM unit {$request->no_lambung} ({$request->jumlah_bbm}L, Odo: {$request->odometer}km)",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Data BBM berhasil dicatat.', 'data' => $logBbm], 201);
        }

        return redirect()->back()->with('success', 'Data konsumsi BBM & odometer berhasil dicatat.');
    }

    /**
     * Tampilkan Detail Log BBM (Read One)
     */
    public function show($id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $log = KonsumsiBbm::findOrFail($id);

        return response()->json(['data' => $log]);
    }

    /**
     * Update Data BBM (Update)
     */
    public function update(Request $request, $id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $log = KonsumsiBbm::findOrFail($id);

        $request->validate([
            'no_lambung' => 'sometimes|required|string|max:50',
            'jenis_kendaraan' => 'sometimes|required|string|max:50',
            'driver' => 'sometimes|required|string|max:100',
            'tanggal' => 'sometimes|required|date',
            'odometer' => 'sometimes|required|numeric|min:0',
            'jumlah_bbm' => 'sometimes|required|numeric|min:1',
            'tipe_bbm' => 'sometimes|required|string|max:50',
        ]);

        $data = $request->only(['no_lambung', 'jenis_kendaraan', 'driver', 'tanggal', 'odometer', 'jumlah_bbm', 'tipe_bbm']);

        $odometer = $request->odometer ?? $log->odometer;
        $jumlahBbm = $request->jumlah_bbm ?? $log->jumlah_bbm;
        $efisiensi = $jumlahBbm > 0 ? round($odometer / $jumlahBbm, 2) : 0;
        $statusEfisiensi = ($efisiensi >= 10 ? 'Efisien' : ($efisiensi >= 6 ? 'Normal' : 'Boros'));

        $data['efisiensi'] = $efisiensi;
        $data['status_efisiensi'] = $statusEfisiensi;

        $log->update($data);

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Update Data BBM',
                'deskripsi' => "Memperbarui log BBM #{$log->id}: {$log->no_lambung}",
            ]);
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Data BBM berhasil diperbarui.', 'data' => $log]);
        }

        return redirect()->back()->with('success', 'Data konsumsi BBM berhasil diperbarui.');
    }

    /**
     * Hapus Data BBM (Delete)
     */
    public function destroy($id)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $log = KonsumsiBbm::findOrFail($id);
        $noLambung = $log->no_lambung;
        $log->delete();

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Hapus Log BBM',
                'deskripsi' => "Menghapus log BBM unit {$noLambung}",
            ]);
        }

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Data konsumsi BBM berhasil dihapus.']);
        }

        return redirect()->back()->with('success', 'Data konsumsi BBM berhasil dihapus.');
    }
}
