<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\KonsumsiBbm;
use App\Models\LogAktivitas;
use App\Models\PemeliharaanKendaraan;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class RekapitulasiController extends Controller
{
    public function index()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        $currentUser = Pengguna::find($userId);
        $userPeran = strtolower($currentUser?->peran ?? session('user_peran', 'admin'));
        $pendingList = Pemesanan::where('status', 'tertunda')->get();

        return view('rekapitulasi.index', compact('userPeran', 'pendingList', 'currentUser'));
    }

    public function export(Request $request)
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        $kategori = strtolower($request->get('kategori', 'pemesanan'));
        $format = strtolower($request->get('format', 'xlsx'));
        $site = $request->get('site', 'site_01');
        $rentangWaktu = $request->get('rentang_waktu', 'month_current');

        $siteLabel = match ($site) {
            'site_02' => 'Morowali',
            'site_all' => 'Semua_Site',
            default => 'Pomalaa'
        };

        $timestamp = date('Ymd_His');
        $filename = "Rekap_{$kategori}_{$siteLabel}_{$timestamp}.".($format === 'pdf' ? 'html' : ($format === 'csv' ? 'csv' : 'xlsx'));

        $userId = session('user_id');
        if ($userId && Pengguna::where('id', $userId)->exists()) {
            LogAktivitas::create([
                'pengguna_id' => $userId,
                'aksi' => 'Ekspor Laporan Rekapitulasi',
                'deskripsi' => "Mengunduh rekapitulasi kategori {$kategori} format {$format} untuk {$siteLabel}",
            ]);
        }

        // Jika format PDF atau permintaan tampilan print report
        if ($format === 'pdf') {
            return $this->exportPrintView($kategori, $siteLabel, $rentangWaktu);
        }

        // Export CSV / Excel Spreadsheet (menggunakan CSV terformat UTF-8 BOM yang langsung dibuka Excel tanpa corrupt)
        return response()->streamDownload(function () use ($kategori) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM untuk kompatibilitas penuh Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            if ($kategori === 'bbm') {
                fputcsv($handle, ['No Lambung', 'Driver', 'Tanggal', 'Odometer (km)', 'BBM (L)', 'Tipe BBM', 'Efisiensi (km/L)', 'Status Efisiensi']);
                $items = KonsumsiBbm::latest()->get();
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->no_lambung,
                        $item->driver,
                        $item->tanggal,
                        $item->odometer,
                        $item->jumlah_bbm,
                        $item->tipe_bbm,
                        $item->efisiensi,
                        $item->status_efisiensi,
                    ]);
                }
            } elseif ($kategori === 'jadwal') {
                fputcsv($handle, ['ID', 'Penanggung Jawab', 'Kategori', 'Judul / Unit', 'Tanggal', 'Waktu / Shift', 'Keterangan']);
                $items = Jadwal::latest()->get();
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->penanggung_jawab,
                        $item->kategori,
                        $item->judul,
                        $item->tanggal,
                        $item->waktu,
                        $item->keterangan ?? '-',
                    ]);
                }
            } elseif ($kategori === 'servis') {
                fputcsv($handle, ['ID', 'Kendaraan', 'Tanggal Servis', 'Kilometer Servis', 'Biaya (Rp)', 'Deskripsi Perawatan']);
                $items = PemeliharaanKendaraan::with('kendaraan')->latest()->get();
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->kendaraan?->nama ?? 'Unit Armada',
                        $item->tanggal_servis,
                        $item->kilometer_servis,
                        $item->biaya,
                        $item->deskripsi,
                    ]);
                }
            } else { // default: pemesanan
                fputcsv($handle, ['No REQ', 'Pemesan', 'Email', 'Lokasi Asal', 'Tujuan', 'Waktu Mulai', 'Waktu Selesai', 'Armada', 'Driver', 'Keperluan', 'Status Pemesanan']);
                $items = Pemesanan::with(['pegawai', 'kendaraan', 'driver'])->latest()->get();
                foreach ($items as $item) {
                    fputcsv($handle, [
                        '#REQ-'.$item->id,
                        $item->pegawai?->nama ?? '-',
                        $item->pegawai?->email ?? '-',
                        $item->lokasi_asal ?? 'Site 01 Pomalaa',
                        $item->tujuan ?? 'Pit West Area',
                        $item->waktu_mulai,
                        $item->waktu_selesai,
                        $item->kendaraan?->nama ?? 'Belum ditentukan',
                        $item->driver?->nama ?? 'Tanpa driver',
                        $item->keperluan,
                        ucfirst($item->status),
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportPrintView(string $kategori, string $siteLabel, string $rentangWaktu)
    {
        $rows = [];
        $headers = [];

        if ($kategori === 'bbm') {
            $headers = ['No Lambung', 'Driver', 'Tanggal', 'Odometer (km)', 'BBM (L)', 'Efisiensi', 'Status'];
            $items = KonsumsiBbm::latest()->get();
            foreach ($items as $i) {
                $rows[] = [$i->no_lambung, $i->driver, $i->tanggal, number_format($i->odometer, 0, ',', '.').' km', $i->jumlah_bbm.' L', $i->efisiensi.' km/L', $i->status_efisiensi];
            }
        } elseif ($kategori === 'jadwal') {
            $headers = ['Penanggung Jawab', 'Kategori', 'Judul / Unit', 'Tanggal', 'Waktu', 'Keterangan'];
            $items = Jadwal::latest()->get();
            foreach ($items as $i) {
                $rows[] = [$i->penanggung_jawab, ucfirst($i->kategori), $i->judul, $i->tanggal, $i->waktu, $i->keterangan ?? '-'];
            }
        } elseif ($kategori === 'servis') {
            $headers = ['Kendaraan', 'Tanggal Servis', 'Kilometer Servis', 'Biaya', 'Deskripsi'];
            $items = PemeliharaanKendaraan::with('kendaraan')->latest()->get();
            foreach ($items as $i) {
                $rows[] = [$i->kendaraan?->nama ?? 'Unit', $i->tanggal_servis, $i->kilometer_servis.' km', 'Rp '.number_format($i->biaya, 0, ',', '.'), $i->deskripsi];
            }
        } else {
            $headers = ['No REQ', 'Pemesan', 'Jadwal Mulai', 'Jadwal Selesai', 'Armada', 'Driver', 'Status'];
            $items = Pemesanan::with(['pegawai', 'kendaraan', 'driver'])->latest()->get();
            foreach ($items as $i) {
                $rows[] = ['#REQ-'.$i->id, $i->pegawai?->nama ?? '-', $i->waktu_mulai, $i->waktu_selesai, $i->kendaraan?->nama ?? '-', $i->driver?->nama ?? '-', ucfirst($i->status)];
            }
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Laporan Rekapitulasi '.strtoupper($kategori).'</title>';
        $html .= '<style>body{font-family:sans-serif;margin:30px;color:#1e293b;}h2{margin-bottom:4px;}p{font-size:12px;color:#64748b;margin-top:0;}table{width:100%;border-collapse:collapse;margin-top:20px;font-size:12px;}th,td{border:1px solid #cbd5e1;padding:8px 10px;text-align:left;}th{background:#f1f5f9;font-weight:bold;}tr:nth-child(even){background:#f8fafc;}@media print{button{display:none;}}</style>';
        $html .= '</head><body>';
        $html .= '<div style="display:flex;justify-content:space-between;align-items:center;">';
        $html .= '<div><h2>Laporan Rekapitulasi '.strtoupper($kategori).'</h2><p>Wilayah: '.htmlspecialchars($siteLabel).' | Dicetak: '.date('d M Y H:i').'</p></div>';
        $html .= '<button onclick="window.print()" style="padding:8px 16px;background:#0048cc;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:bold;">Cetak / Simpan PDF</button>';
        $html .= '</div>';
        $html .= '<table><thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>'.htmlspecialchars($h).'</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr>';
            foreach ($r as $c) {
                $html .= '<td>'.htmlspecialchars($c).'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
