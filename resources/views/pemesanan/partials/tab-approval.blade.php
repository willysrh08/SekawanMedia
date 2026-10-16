<!-- DAFTAR ANTREAN APPROVAL 2-TAHAP -->
<div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs flex flex-col gap-5 border border-outline-variant/40">
    <div class="flex items-center justify-between border-b border-surface-container pb-3">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">pending_actions</span>
            <h2 class="font-headline font-semibold text-base text-on-surface">Daftar Menunggu Persetujuan</h2>
        </div>
        <span class="text-xs text-secondary font-semibold">{{ $pendingList->count() }} Permohonan Tertunda</span>
    </div>

    <div class="flex flex-col gap-4">
        @forelse($pendingList as $item)
            @php
                $lvl1 = $item->persetujuan->where('level', 1)->first();
                $lvl2 = $item->persetujuan->where('level', 2)->first();

                $lvl1Status = $lvl1 ? $lvl1->status : 'tertunda';
                $lvl2Status = $lvl2 ? $lvl2->status : 'tertunda';

                $lvl1Approved = ($lvl1Status === 'disetujui' || $lvl1Status === 'dilewati');
            @endphp

            <div class="p-5 rounded-2xl bg-surface-container-low/70 border border-outline-variant/40 flex flex-col gap-4 hover:border-primary/40 transition-all">
                
                <!-- BARIS ATAS: HEADER REQ -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-outline-variant/30 pb-3">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="px-2.5 py-1 rounded-lg bg-primary-container text-white font-bold text-xs">#REQ-{{ $item->id }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $lvl1Approved ? 'bg-sky-100 text-sky-800 border border-sky-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                            {{ $lvl1Approved ? 'Menunggu persetujuan atasan' : 'Menunggu persetujuan' }}
                        </span>
                        <span class="text-xs font-bold text-on-surface">{{ $item->pegawai->nama ?? 'Pegawai' }}</span>
                        <span class="text-[11px] text-secondary">({{ $item->pegawai->email ?? '-' }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-secondary flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">schedule</span>
                            Diajukan: {{ $item->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>

                <!-- RINCIAN PEMESANAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs bg-white/70 p-3.5 rounded-xl border border-outline-variant/20">
                    <div>
                        <span class="text-secondary text-[11px] block">Jadwal Tugas</span>
                        <span class="font-semibold text-on-surface">{{ date('d M Y H:i', strtotime($item->waktu_mulai)) }} s/d {{ date('d M Y H:i', strtotime($item->waktu_selesai)) }}</span>
                    </div>
                    <div>
                        <span class="text-secondary text-[11px] block">Keperluan / Penugasan</span>
                        <span class="font-semibold text-on-surface">{{ $item->keperluan }}</span>
                    </div>
                    <div>
                        <span class="text-secondary text-[11px] block">Armada & Supir Ditugaskan</span>
                        <span class="font-semibold text-on-surface">
                            {{ $item->kendaraan ? $item->kendaraan->nama . ' (' . $item->kendaraan->plat_nomor . ')' : 'Belum ditetapkan' }}
                            /
                            {{ $item->driver ? $item->driver->nama : 'Tanpa supir' }}
                        </span>
                    </div>
                </div>

                <!-- STATUS PIPELINE 2 TAHAP -->
                <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/30 flex flex-col gap-2">
                    <span class="text-[11px] font-bold text-secondary uppercase tracking-wider">Status Otorisasi:</span>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- STEP 1 STATUS -->
                        <div class="p-3 rounded-lg border {{ $lvl1Status === 'disetujui' ? 'bg-emerald-50 border-emerald-300' : ($lvl1Status === 'dilewati' ? 'bg-purple-50 border-purple-300' : ($lvl1Status === 'ditolak' ? 'bg-rose-50 border-rose-300' : 'bg-amber-50 border-amber-300')) }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 font-bold text-xs">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] {{ $lvl1Status === 'disetujui' ? 'bg-emerald-600 text-white' : ($lvl1Status === 'dilewati' ? 'bg-purple-600 text-white' : 'bg-amber-500 text-white') }}">1</span>
                                    <span>Tahap 1: Atasan Langsung</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $lvl1Status === 'disetujui' ? 'bg-emerald-200 text-emerald-900' : ($lvl1Status === 'dilewati' ? 'bg-purple-200 text-purple-900' : ($lvl1Status === 'ditolak' ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900')) }}">
                                    {{ $lvl1Status === 'dilewati' ? 'Dilewati (Bypass Manajer)' : ucfirst($lvl1Status) }}
                                </span>
                            </div>
                            @if($lvl1 && $lvl1->catatan)
                                <p class="text-[11px] text-secondary mt-1.5 italic">"{{ $lvl1->catatan }}"</p>
                            @endif
                        </div>

                        <!-- STEP 2 STATUS -->
                        <div class="p-3 rounded-lg border {{ $lvl2Status === 'disetujui' ? 'bg-emerald-50 border-emerald-300' : ($lvl2Status === 'ditolak' ? 'bg-rose-50 border-rose-300' : 'bg-amber-50 border-amber-300') }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 font-bold text-xs">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] {{ $lvl2Status === 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }}">2</span>
                                    <span>Tahap 2: Manajer</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $lvl2Status === 'disetujui' ? 'bg-emerald-200 text-emerald-900' : ($lvl2Status === 'ditolak' ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900') }}">
                                    {{ ucfirst($lvl2Status) }}
                                </span>
                            </div>
                            @if($lvl2 && $lvl2->catatan)
                                <p class="text-[11px] text-secondary mt-1.5 italic">"{{ $lvl2->catatan }}"</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- FORM AKSI APPROVAL SESUAI PERAN LOGIN -->
                <div class="pt-2 flex flex-col gap-3">
                    
                    {{-- KONDISI 1: JIKA LOGIN SEBAGAI MANAJER (HAK KHUSUS BYPASS) --}}
                    @if($isManajer)
                        <div class="p-4 rounded-xl bg-purple-50/80 border border-purple-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-1.5 text-purple-900 font-bold text-xs">
                                    <span class="material-symbols-outlined text-purple-700 text-base">verified</span>
                                    <span>Otorisasi Eksekutif Manajer</span>
                                </div>
                                <p class="text-[11px] text-purple-800 mt-0.5">
                                    Anda dapat langsung menyetujui pemesanan ini secara penuh. 
                                    @if($lvl1Status === 'tertunda')
                                        <em>(Tahap Atasan akan otomatis dilewati).</em>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <!-- FORM TOLAK OLEH MANAJER -->
                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="ditolak">
                                    <button class="px-3.5 py-2 rounded-lg bg-white text-rose-700 hover:bg-rose-50 border border-rose-200 font-semibold text-xs transition-colors cursor-pointer" type="submit">
                                        Tolak
                                    </button>
                                </form>

                                <!-- FORM SETUJUI OLEH MANAJER (LANGSUNG) -->
                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="disetujui">
                                    <button class="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-all cursor-pointer" type="submit">
                                        <span>Setujui Langsung</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    {{-- KONDISI 2: JIKA LOGIN SEBAGAI ADMIN --}}
                    @elseif($isAdmin)
                        <div class="p-3 rounded-xl bg-surface-container text-xs text-secondary italic">
                            Akun Admin bertugas murni menginput pemesanan serta menetapkan armada dan driver. Otorisasi persetujuan berjenjang dilakukan oleh Atasan (Tahap 1) dan Manajer (Tahap 2).
                        </div>

                    {{-- KONDISI 3: JIKA LOGIN SEBAGAI ATASAN (LEVEL 1 APPROVER) --}}
                    @elseif($isAtasan)
                        <div class="p-4 rounded-xl bg-amber-50/80 border-amber-200 border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-1.5 text-on-surface font-bold text-xs">
                                    <span class="material-symbols-outlined text-amber-700 text-base">supervisor_account</span>
                                    <span>Otorisasi Tahap 1 (Atasan Langsung)</span>
                                </div>
                                <p class="text-[11px] text-secondary mt-0.5">
                                    @if($lvl1Status === 'disetujui')
                                        <span class="text-emerald-700 font-semibold">✓ Anda telah menyetujui Tahap 1.</span> Menunggu persetujuan akhir dari Manajer (Tahap 2).
                                    @elseif($lvl1Status === 'ditolak')
                                        <span class="text-rose-700 font-semibold">✕ Permohonan telah ditolak.</span>
                                    @else
                                        Silakan periksa dan berikan persetujuan permohonan dinas ini sebagai Atasan (Tahap 1).
                                    @endif
                                </p>
                            </div>

                            @if($lvl1Status === 'tertunda')
                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="ditolak">
                                    <button class="px-3.5 py-2 rounded-lg bg-white text-rose-700 hover:bg-rose-50 border border-rose-200 font-semibold text-xs transition-colors cursor-pointer" type="submit">
                                        Tolak
                                    </button>
                                </form>

                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="disetujui">
                                    <button class="flex items-center gap-1 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-all cursor-pointer" type="submit">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                        <span>Setujui Tahap 1 (Atasan)</span>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>

                    {{-- KONDISI 4: PEGAWAI BIASA --}}
                    @else
                        <div class="p-3 rounded-xl bg-surface-container text-xs text-secondary italic">
                            Anda sedang masuk sebagai akun Pegawai. Tindakan otorisasi persetujuan berjenjang dilakukan oleh Atasan (Tahap 1) atau Manajer (Tahap 2).
                        </div>
                    @endif

                </div>

            </div>
        @empty
            <div class="text-center py-12 flex flex-col items-center justify-center gap-2 text-secondary">
                <span class="material-symbols-outlined text-4xl text-secondary/50">task_alt</span>
                <span class="text-sm font-semibold text-on-surface">Tidak ada antrean persetujuan</span>
                <span class="text-xs">Semua permohonan pemesanan kendaraan telah diproses atau belum ada pengajuan baru.</span>
            </div>
        @endforelse
    </div>
</div>
