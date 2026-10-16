<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekspor Rekapitulasi - DriveControl VMS</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "secondary-fixed": "#dae2fd",
                        "primary": "#0048cc",
                        "on-primary-fixed-variant": "#003cac",
                        "background": "#f8f9ff",
                        "on-secondary-fixed-variant": "#3f465c",
                        "on-primary": "#ffffff",
                        "on-error-container": "#93000a",
                        "error": "#ba1a1a",
                        "tertiary-fixed": "#cce5ff",
                        "primary-fixed-dim": "#b5c4ff",
                        "error-container": "#ffdad6",
                        "on-surface": "#0b1c30",
                        "on-secondary-container": "#5c647a",
                        "secondary": "#565e74",
                        "surface-dim": "#cbdbf5",
                        "on-secondary-fixed": "#131b2e",
                        "secondary-fixed-dim": "#bec6e0",
                        "surface": "#f8f9ff",
                        "surface-container-low": "#eff4ff",
                        "tertiary-fixed-dim": "#93ccff",
                        "on-tertiary": "#ffffff",
                        "on-primary-fixed": "#00164d",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-container": "#e7f2ff",
                        "inverse-on-surface": "#eaf1ff",
                        "surface-container-high": "#dce9ff",
                        "tertiary-container": "#0073ae",
                        "primary-fixed": "#dce1ff",
                        "surface-container": "#e5eeff",
                        "inverse-surface": "#213145",
                        "on-error": "#ffffff",
                        "outline": "#737687",
                        "surface-bright": "#f8f9ff",
                        "on-tertiary-fixed-variant": "#004b73",
                        "surface-variant": "#d3e4fe",
                        "surface-container-highest": "#d3e4fe",
                        "surface-tint": "#0051e1",
                        "inverse-primary": "#b5c4ff",
                        "on-secondary": "#ffffff",
                        "on-background": "#0b1c30",
                        "outline-variant": "#c3c5d8",
                        "on-primary-container": "#eeefff",
                        "primary-container": "#1e60f6",
                        "on-surface-variant": "#434655",
                        "secondary-container": "#dae2fd",
                        "on-tertiary-fixed": "#001d31",
                        "tertiary": "#005a89"
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; overscroll-behavior: none; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-background text-on-surface antialiased h-full flex flex-col overflow-hidden">

    <!-- TOP HEADER -->
    <header class="sticky top-0 z-50 bg-surface-container-lowest border-b border-outline-variant/50 px-4 lg:px-6 h-16 flex items-center justify-between shadow-xs flex-shrink-0">
        <div class="flex items-center gap-3">
            <button id="sidebarToggle" class="p-2 -ml-2 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors cursor-pointer" title="Toggle Navigasi" type="button">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="rounded-xl overflow-hidden flex items-center justify-center w-12 h-12">
                    <img src="{{ asset('images/logo.png') }}" alt="DriveControl Logo" class="w-full h-full object-contain" onerror="this.src='https://lh3.googleusercontent.com/aida-public/AB6AXuDmHihwX0Xm0V8DdNQavfK0x9ZGDzO2Mqc86H4lUyEzMKEPuzsotPQfEtVNeLArBuZ8dBk5u8Rm_mDdni5C9IixWHJ4XMCEAgLEQF15y3b1C7YutBt_A80Ii4_TG4GvSw5PocWd3ehRvlHSQOOQa3ACV4tFhotwynch0apAOA-qg_NSkw5HFNOHfDwnTbxQ3Vtbw-TmXy1_KNGh6fVm2fgm5UoILHKHRfea1J8HE13UliQsTp0tVE8IsP1gNwVpKnS02VE'">
                </div>
                <span class="font-headline font-bold tracking-tight text-lg bg-gradient-to-r from-primary to-on-surface bg-clip-text text-transparent">DriveControl</span>
            </div>
        </div>

        <div class="hidden md:flex items-center">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container-low border border-outline-variant/40 text-xs">
                <span class="material-symbols-outlined text-primary text-sm">location_on</span>
                <span class="font-medium text-secondary">Wilayah Operasional:</span>
                <select class="bg-transparent font-semibold text-on-surface focus:outline-none cursor-pointer pr-4 text-xs py-0 border-none">
                    <option selected>Site 01 - Pomalaa (Nikel Ore)</option>
                    <option>Site 02 - Morowali (Processing Plant)</option>
                    <option>Site 03 - Halmahera (Hauling Pit)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <button class="md:hidden p-2 rounded-lg text-secondary hover:bg-surface-container-low cursor-pointer" title="Pilih Site" type="button"><span class="material-symbols-outlined text-xl">location_on</span></button>
            @include('partials.header-notification')
            <div class="h-6 w-px bg-outline-variant/60 mx-1 hidden sm:block"></div>

            <div class="flex items-center gap-2.5 pl-1">
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-primary to-primary-container text-white font-bold flex items-center justify-center text-xs shadow-xs">
                    {{ strtoupper(substr(session('user_nama', 'User'), 0, 2)) }}
                </div>
                <div class="hidden xl:flex flex-col text-left">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-on-surface">{{ session('user_nama', 'Aqwila') }}</span>
                        @php
                            $userPeran =$userPeran ?? session('user_peran', 'atasan');
                            $roleBadgeColor = match($userPeran) {
                                'manajer' => 'bg-purple-100 text-purple-800',
                                'atasan' => 'bg-amber-100 text-amber-800',
                                'admin' => 'bg-blue-100 text-blue-800',
                                default => 'bg-emerald-100 text-emerald-800'
                            };
                        @endphp
                        <span class="text-[10px] px-1 py-0.2 rounded {{ $roleBadgeColor }} font-semibold uppercase">{{ $userPeran }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ url('/logout') }}" method="POST" class="ml-2">
                @csrf
                <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors cursor-pointer" title="Keluar">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </button>
            </form>
        </div>
    </header>

    <!-- LAYOUT UTAMA (SIDEBAR & KONTEN) -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- SIDEBAR NAVIGASI -->
        <aside id="mainSidebar" class="flex-shrink-0 bg-surface-container-lowest border-r border-outline-variant/40 flex flex-col justify-between transition-all duration-300 w-64 z-40 overflow-y-auto">
            <div class="p-4 space-y-6">
                <div class="space-y-1">
                    <p class="sidebar-text px-3 text-[11px] font-bold uppercase tracking-wider text-secondary/80 mb-2">Navigasi Utama</p>
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">dashboard</span><span class="sidebar-text truncate">Dashboard Utama</span>
                    </a>
                    <a href="{{ url('/pemesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">assignment</span>
                        <span class="sidebar-text truncate">Pemesanan Kendaraan</span>
                        @if(isset($pendingList) &&$pendingList->count() > 0)
                            <span class="sidebar-text ml-auto bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                {{ $pendingList->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ url('/armada') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">directions_car</span><span class="sidebar-text truncate">Armada Tambang</span>
                    </a>
                    <a href="{{ url('/jadwal') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">engineering</span><span class="sidebar-text truncate">Jadwal Driver & Servis</span>
                    </a>
                </div>
                <div class="space-y-1 pt-2 border-t border-outline-variant/30">
                    <p class="sidebar-text px-3 text-[11px] font-bold uppercase tracking-wider text-secondary/80 mb-2">Analitik & Laporan</p>
                    <a href="{{ url('/bbm') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">local_gas_station</span><span class="sidebar-text truncate">Konsumsi BBM & Odo</span>
                    </a>
                    <a href="{{ url('/rekapitulasi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary-container/10 text-primary font-semibold text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">file_download</span><span class="sidebar-text truncate">Ekspor Rekapitulasi</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- KONTEN UTAMA HALAMAN -->
        <main class="flex-1 overflow-y-auto bg-background px-4 lg:px-8 py-6">
            <div class="flex flex-col w-full pb-16 max-w-7xl mx-auto">
                
                <!-- Breadcrumb & Page Title -->
                <div class="flex flex-col gap-1 mb-8 pt-2">
                </div>

                <!-- Main Configuration Container -->
                <form id="exportForm" action="{{ url('/rekapitulasi/export') }}" method="GET" class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-xs p-6 lg:p-8 mb-8">
                    <input type="hidden" name="kategori" id="selectedKategori" value="pemesanan">
                    <div class="flex flex-col gap-6">
                        <div>
                            <h2 class="text-base font-semibold text-on-surface mb-3">Pilih Kategori</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" id="categoryGroup">
                                <div data-category="pemesanan" class="category-card group relative p-4 rounded-xl cursor-pointer transition-all flex items-center justify-between ring-2 ring-primary bg-primary/[0.03]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary-container text-on-primary flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[20px]">assignment</span>
                                        </div>
                                        <span class="text-sm font-semibold text-on-surface">Pemesanan</span>
                                    </div>
                                    <span class="material-symbols-outlined text-[20px] check-icon text-primary">check_circle</span>
                                </div>
                                <div data-category="bbm" class="category-card group relative p-4 rounded-xl bg-surface-container-low hover:bg-surface-container cursor-pointer transition-all flex items-center justify-between border border-outline-variant/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-surface-container-high text-on-surface flex items-center justify-center group-hover:bg-primary-fixed group-hover:text-on-primary-fixed transition-colors">
                                            <span class="material-symbols-outlined text-[20px]">local_gas_station</span>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">BBM & Odometer</span>
                                    </div>
                                    <span class="material-symbols-outlined text-outline-variant text-[20px] check-icon">radio_button_unchecked</span>
                                </div>
                                <div data-category="jadwal" class="category-card group relative p-4 rounded-xl bg-surface-container-low hover:bg-surface-container cursor-pointer transition-all flex items-center justify-between border border-outline-variant/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-surface-container-high text-on-surface flex items-center justify-center group-hover:bg-primary-fixed group-hover:text-on-primary-fixed transition-colors">
                                            <span class="material-symbols-outlined text-[20px]">badge</span>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">Jadwal & Driver</span>
                                    </div>
                                    <span class="material-symbols-outlined text-outline-variant text-[20px] check-icon">radio_button_unchecked</span>
                                </div>
                                <div data-category="servis" class="category-card group relative p-4 rounded-xl bg-surface-container-low hover:bg-surface-container cursor-pointer transition-all flex items-center justify-between border border-outline-variant/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-surface-container-high text-on-surface flex items-center justify-center group-hover:bg-primary-fixed group-hover:text-on-primary-fixed transition-colors">
                                            <span class="material-symbols-outlined text-[20px]">build_circle</span>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface">Servis & Perawatan</span>
                                    </div>
                                    <span class="material-symbols-outlined text-outline-variant text-[20px] check-icon">radio_button_unchecked</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <div>
                                <label class="text-sm text-on-surface font-medium block mb-2">Rentang Waktu</label>
                                <div class="relative">
                                    <select name="rentang_waktu" class="w-full h-11 px-4 pr-10 rounded-lg bg-surface-container-low border border-outline-variant/40 text-sm text-on-surface appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                                        <option selected value="month_current">Bulan Ini (Mei 2025)</option>
                                        <option value="q2_2025">Kuartal II 2025</option>
                                        <option value="month_prev">Bulan Lalu (April 2025)</option>
                                        <option value="year_2025">Tahun Berjalan 2025</option>
                                        <option value="custom">Kustom Rentang Tanggal...</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-3 text-[20px] text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-on-surface font-medium block mb-2">Site / Lokasi</label>
                                <div class="relative">
                                    <select name="site" class="w-full h-11 px-4 pr-10 rounded-lg bg-surface-container-low border border-outline-variant/40 text-sm text-on-surface appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                                        <option selected value="site_01">Site 01 - Pomalaa</option>
                                        <option value="site_02">Site 02 - Morowali</option>
                                        <option value="site_all">Semua Site Tambang</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-3 text-[20px] text-on-surface-variant pointer-events-none">pin_drop</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-on-surface font-medium block mb-2">Format Berkas</label>
                                <div class="relative">
                                    <select name="format" class="w-full h-11 px-4 pr-10 rounded-lg bg-surface-container-low border border-outline-variant/40 text-sm text-on-surface appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                                        <option selected value="xlsx">Excel Spreadsheet (.xlsx)</option>
                                        <option value="pdf">Dokumen PDF (.pdf)</option>
                                        <option value="csv">CSV Raw Data (.csv)</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-3 text-[20px] text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-surface-container">
                            <button class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-on-primary font-semibold text-sm shadow-sm hover:shadow transition-all cursor-pointer" id="btnDownload" type="button">
                                <span class="material-symbols-outlined text-[20px]">download</span>
                                <span>Unduh Rekapitulasi</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Riwayat Unduhan Terakhir Section -->
                <div class="flex flex-col">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-on-surface">Riwayat Unduhan</h2>
                    </div>
                    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-surface-container-low text-xs font-semibold text-on-surface-variant border-b border-outline-variant/30">
                                        <th class="py-3.5 px-6" scope="col">Nama Berkas</th>
                                        <th class="py-3.5 px-6" scope="col">Tanggal</th>
                                        <th class="py-3.5 px-6" scope="col">Format</th>
                                        <th class="py-3.5 px-6 text-right" scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-container">
                                    <tr class="hover:bg-surface-container-low/60 transition-colors">
                                        <td class="py-4 px-6 font-medium text-on-surface">Rekap_BBM_Odometer_Mei2025.xlsx</td>
                                        <td class="py-4 px-6 text-on-surface-variant text-xs">21 Mei 2025, 09:15</td>
                                        <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">.XLSX</span></td>
                                        <td class="py-4 px-6 text-right"><a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-primary hover:bg-surface-container font-medium text-xs transition-colors" href="{{ url('/rekapitulasi/export?kategori=bbm&format=xlsx') }}"><span class="material-symbols-outlined text-[16px]">download</span>Unduh</a></td>
                                    </tr>
                                    <tr class="hover:bg-surface-container-low/60 transition-colors">
                                        <td class="py-4 px-6 font-medium text-on-surface">Laporan_Operasional_Minggu_3.pdf</td>
                                        <td class="py-4 px-6 text-on-surface-variant text-xs">19 Mei 2025, 16:40</td>
                                        <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-100 text-rose-800">.PDF</span></td>
                                        <td class="py-4 px-6 text-right"><a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-primary hover:bg-surface-container font-medium text-xs transition-colors" href="{{ url('/rekapitulasi/export?kategori=pemesanan&format=pdf') }}"><span class="material-symbols-outlined text-[16px]">download</span>Unduh</a></td>
                                    </tr>
                                    <tr class="hover:bg-surface-container-low/60 transition-colors">
                                        <td class="py-4 px-6 font-medium text-on-surface">Log_Servis_Armada_Q1.xlsx</td>
                                        <td class="py-4 px-6 text-on-surface-variant text-xs">14 Mei 2025, 11:20</td>
                                        <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">.XLSX</span></td>
                                        <td class="py-4 px-6 text-right"><a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-primary hover:bg-surface-container font-medium text-xs transition-colors" href="{{ url('/rekapitulasi/export?kategori=servis&format=xlsx') }}"><span class="material-symbols-outlined text-[16px]">download</span>Unduh</a></td>
                                    </tr>
                                    <tr class="hover:bg-surface-container-low/60 transition-colors">
                                        <td class="py-4 px-6 font-medium text-on-surface">Presensi_Driver_ShiftA_April2025.csv</td>
                                        <td class="py-4 px-6 text-on-surface-variant text-xs">02 Mei 2025, 14:05</td>
                                        <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">.CSV</span></td>
                                        <td class="py-4 px-6 text-right"><a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-primary hover:bg-surface-container font-medium text-xs transition-colors" href="{{ url('/rekapitulasi/export?kategori=jadwal&format=csv') }}"><span class="material-symbols-outlined text-[16px]">download</span>Unduh</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Script Interaktif Kategori & Tombol Unduh -->
    <script>
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach(card => {
            card.addEventListener('click', () => {
                categoryCards.forEach(c => {
                    c.classList.remove('ring-2', 'ring-primary', 'bg-primary/[0.03]');
                    c.classList.add('bg-surface-container-low');
                    const icon = c.querySelector('.check-icon');
                    icon.textContent = 'radio_button_unchecked';
                    icon.classList.remove('text-primary');
                    icon.classList.add('text-outline-variant');
                    
                    const avatar = c.querySelector('.w-10');
                    avatar.className = 'w-10 h-10 rounded-lg bg-surface-container-high text-on-surface flex items-center justify-center group-hover:bg-primary-fixed group-hover:text-on-primary-fixed transition-colors';
                });

                card.classList.remove('bg-surface-container-low');
                card.classList.add('ring-2', 'ring-primary', 'bg-primary/[0.03]');
                const activeIcon = card.querySelector('.check-icon');
                activeIcon.textContent = 'check_circle';
                activeIcon.classList.remove('text-outline-variant');
                activeIcon.classList.add('text-primary');

                const activeAvatar = card.querySelector('.w-10');
                activeAvatar.className = 'w-10 h-10 rounded-lg bg-primary-container text-on-primary flex items-center justify-center';

                const catVal = card.getAttribute('data-category');
                const catInput = document.getElementById('selectedKategori');
                if (catInput && catVal) {
                    catInput.value = catVal;
                }
            });
        });

        const btnDownload = document.getElementById('btnDownload');
        if (btnDownload) {
            btnDownload.addEventListener('click', () => {
                const origText = btnDownload.innerHTML;
                btnDownload.innerHTML = `<span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span><span>Membuat Berkas...</span>`;
                btnDownload.disabled = true;
                setTimeout(() => {
                    btnDownload.innerHTML = `<span class="material-symbols-outlined text-[20px]">check</span><span>Berkas Siap Diunduh</span>`;
                    const form = document.getElementById('exportForm');
                    if (form) form.submit();
                    setTimeout(() => {
                        btnDownload.innerHTML = origText;
                        btnDownload.disabled = false;
                    }, 2000);
                }, 800);
            });
        }

        // Toggle Sidebar untuk layar mobile/responsif
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainSidebar = document.getElementById('mainSidebar');
        if (sidebarToggle && mainSidebar) {
            sidebarToggle.addEventListener('click', () => {
                mainSidebar.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>