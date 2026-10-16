<!-- TAB RIWAYAT & MONITORING SEMUA PEMESANAN -->
<div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
    <div class="flex items-center justify-between border-b border-surface-container pb-3">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">history</span>
            <h2 class="font-headline font-semibold text-base text-on-surface">Monitoring Semua Pemesanan & Status Approval</h2>
        </div>
        <span class="text-xs text-secondary font-semibold">Total {{ $riwayatList->count() }} Data</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-outline-variant/40 text-secondary font-semibold uppercase text-[10px] tracking-wider">
                    <th class="py-3 px-3">No. REQ / Pemesan</th>
                    <th class="py-3 px-3">Jadwal Tugas</th>
                    <th class="py-3 px-3">Keperluan</th>
                    <th class="py-3 px-3">Armada & Driver</th>
                    <th class="py-3 px-3 text-center">Tahap 1 (Atasan)</th>
                    <th class="py-3 px-3 text-center">Tahap 2 (Manajer)</th>
                    <th class="py-3 px-3 text-right">Status Pemesanan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20">
                @forelse($riwayatList as $row)
                    @php
                        $s1 = $row->persetujuan->where('level', 1)->first();
                        $s2 = $row->persetujuan->where('level', 2)->first();
                        
                        $c1 = match($s1?->status) {
                            'disetujui' => 'bg-emerald-100 text-emerald-800',
                            'dilewati' => 'bg-purple-100 text-purple-800',
                            'ditolak' => 'bg-rose-100 text-rose-800',
                            default => 'bg-amber-100 text-amber-800'
                        };

                        $c2 = match($s2?->status) {
                            'disetujui' => 'bg-emerald-100 text-emerald-800',
                            'ditolak' => 'bg-rose-100 text-rose-800',
                            default => 'bg-amber-100 text-amber-800'
                        };

                        $statusText = match($row->status) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => ($s1 && ($s1->status === 'disetujui' || $s1->status === 'dilewati')) ? 'Menunggu persetujuan manajer' : 'Menunggu persetujuan atasan'
                        };
                        $statusBadge = match($row->status) {
                            'disetujui' => 'bg-emerald-600 text-white',
                            'ditolak' => 'bg-rose-600 text-white',
                            default => ($s1 && ($s1->status === 'disetujui' || $s1->status === 'dilewati')) ? 'bg-sky-600 text-white' : 'bg-amber-500 text-white'
                        };
                    @endphp
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-3 px-3">
                            <div class="font-bold text-primary">#REQ-{{ $row->id }}</div>
                            <div class="text-[11px] text-on-surface font-medium">{{ $row->pegawai->nama ?? 'Pegawai' }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-medium text-on-surface">{{ date('d M Y, H:i', strtotime($row->waktu_mulai)) }}</div>
                            <div class="text-[11px] text-secondary">s/d {{ date('d M Y, H:i', strtotime($row->waktu_selesai)) }}</div>
                        </td>
                        <td class="py-3 px-3 max-w-[200px]">
                            <div class="font-medium text-on-surface truncate" title="{{ $row->keperluan }}">{{ $row->keperluan }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-medium text-on-surface">{{ $row->kendaraan->nama ?? 'Belum ditentukan' }}</div>
                            <div class="text-[11px] text-secondary">{{ $row->driver->nama ?? 'Tanpa supir' }}</div>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $c1 }}">
                                {{ $s1?->status === 'dilewati' ? '⚡ Bypass Manajer' : ($s1?->status ?? 'Tertunda') }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $c2 }}">
                                {{ $s2?->status ?? 'Tertunda' }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $statusBadge }}">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-secondary text-xs">
                            Belum ada riwayat permohonan pemesanan kendaraan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
