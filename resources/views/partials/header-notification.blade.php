@php
    $headerPendingList = isset($pendingList) 
        ? $pendingList 
        : \App\Models\Pemesanan::with(['pegawai', 'kendaraan', 'driver', 'persetujuan'])
            ->where('status', 'tertunda')
            ->latest()
            ->get();
    $headerNotifCount = $headerPendingList->count();
@endphp

<div class="relative inline-block text-left" id="headerNotifDropdownWrapper">
    <!-- Tombol Notifikasi Header -->
    <button 
        type="button" 
        id="headerNotifDropdownBtn" 
        class="relative p-2 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors cursor-pointer focus:outline-none" 
        title="Notifikasi Persetujuan"
        aria-expanded="false"
    >
        <span class="material-symbols-outlined text-xl">notifications</span>
        @if($headerNotifCount > 0)
            <span class="absolute top-1 right-1 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-error text-[10px] font-bold text-white ring-2 ring-white animate-pulse">
                {{ $headerNotifCount }}
            </span>
        @endif
    </button>

    <!-- Menu Dropdown Notifikasi -->
    <div 
        id="headerNotifDropdownMenu" 
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/40 z-50 overflow-hidden transition-all duration-200"
    >
        <!-- Header Dropdown -->
        <div class="p-4 bg-surface-container-low border-b border-outline-variant/30 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">fact_check</span>
                <span class="font-headline font-bold text-sm text-on-surface">Persetujuan Pemesanan</span>
            </div>
            @if($headerNotifCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                    {{ $headerNotifCount }} Menunggu
                </span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    Semua Disetujui
                </span>
            @endif
        </div>

        <!-- Daftar Notifikasi Item -->
        <div class="max-h-80 overflow-y-auto divide-y divide-outline-variant/20">
            @forelse($headerPendingList->take(5) as $notifItem)
                @php
                    $pLvl1 = $notifItem->persetujuan->where('level', 1)->first();
                    $pLvl1Status = $pLvl1 ? $pLvl1->status : 'tertunda';
                    $pLvl1Passed = ($pLvl1Status === 'disetujui' || $pLvl1Status === 'dilewati');
                @endphp
                <a href="{{ url('/pemesanan?tab=approval') }}" class="p-3.5 hover:bg-surface-container-low/70 transition-colors flex items-start gap-3 block group">
                    <div class="w-8 h-8 rounded-full {{ $pLvl1Passed ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center shrink-0 mt-0.5">
                        <span class="material-symbols-outlined text-base">pending_actions</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="font-bold text-xs text-primary group-hover:underline">#REQ-{{ $notifItem->id }}</span>
                            <span class="text-[10px] text-secondary">{{ $notifItem->created_at ? $notifItem->created_at->diffForHumans() : 'Baru saja' }}</span>
                        </div>
                        <p class="text-xs font-semibold text-on-surface truncate">
                            {{ $notifItem->pegawai->nama ?? 'Pegawai Tambang' }}
                        </p>
                        <p class="text-[11px] text-secondary line-clamp-1">
                            {{ $notifItem->keperluan ?? 'Penugasan Operasional' }}
                        </p>
                        <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium {{ $pLvl1Passed ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $pLvl1Passed ? 'Butuh Persetujuan Manajer (L2)' : 'Butuh Persetujuan Atasan (L1)' }}
                            </span>
                            @if($notifItem->kendaraan)
                                <span class="text-[10px] text-secondary">
                                    • {{ $notifItem->kendaraan->merek }} {{ $notifItem->kendaraan->model }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-8 px-4 text-center flex flex-col items-center justify-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                    <span class="text-xs font-bold text-on-surface">Tidak Ada Persetujuan Tertunda</span>
                    <span class="text-[11px] text-secondary mt-0.5">Semua permohonan kendaraan operasional telah diproses.</span>
                </div>
            @endforelse
        </div>

        <!-- Footer Dropdown -->
        <div class="p-3 bg-surface-container-low/50 border-t border-outline-variant/30 text-center">
            <a href="{{ url('/pemesanan?tab=approval') }}" class="inline-flex items-center justify-center gap-1 text-xs font-bold text-primary hover:text-primary/80 transition-colors py-1 w-full">
                <span>Lihat Semua di Menu Persetujuan</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>
</div>

<script>
    if (!window.headerNotifDropdownInit) {
        window.headerNotifDropdownInit = true;
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('#headerNotifDropdownBtn');
            const menu = document.getElementById('headerNotifDropdownMenu');
            const wrapper = document.getElementById('headerNotifDropdownWrapper');

            if (btn) {
                e.stopPropagation();
                if (menu) {
                    menu.classList.toggle('hidden');
                    const isExpanded = !menu.classList.contains('hidden');
                    btn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                }
            } else if (wrapper && !wrapper.contains(e.target)) {
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    const toggleBtn = document.getElementById('headerNotifDropdownBtn');
                    if (toggleBtn) {
                        toggleBtn.setAttribute('aria-expanded', 'false');
                    }
                }
            }
        });
    }
</script>
