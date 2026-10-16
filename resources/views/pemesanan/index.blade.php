<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Kendaraan - DriveControl</title>
    <style>
        @layer base {
            html, body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
            body { overscroll-behavior: none; }
            main>:first-child { margin-top: 0 !important; }
            main>:last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0048cc",
                        "primary-container": "#1e60f6",
                        "on-primary": "#ffffff",
                        "secondary": "#565e74",
                        "secondary-container": "#dae2fd",
                        "background": "#f8f9ff",
                        "surface": "#f8f9ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#eff4ff",
                        "surface-container": "#e5eeff",
                        "surface-container-high": "#dce9ff",
                        "surface-container-highest": "#d3e4fe",
                        "on-surface": "#0b1c30",
                        "on-surface-variant": "#434655",
                        "outline": "#737687",
                        "outline-variant": "#c3c5d8",
                        "tertiary": "#005a89",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error": "#ffffff",
                        "on-error-container": "#93000a"
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                        headline: ["Plus Jakarta Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-on-surface min-h-screen flex flex-col font-sans antialiased">

@php
    $userId = session('user_id');
    $currentUser = \App\Models\Pengguna::find($userId);
    $userEmail = strtolower($currentUser?->email ?? session('user_email', ''));
    $userPeran = strtolower($currentUser?->peran ?? session('user_peran', 'pegawai'));

    if (str_contains($userEmail, 'manajer')) {
        $userPeran = 'manajer';
    } elseif (str_contains($userEmail, 'atasan')) {
        $userPeran = 'atasan';
    } elseif (str_contains($userEmail, 'admin')) {
        $userPeran = 'admin';
    } elseif (str_contains($userEmail, 'pegawai')) {
        $userPeran = 'pegawai';
    }

    $isManajer = ($userPeran === 'manajer');
    $isAdmin = ($userPeran === 'admin');
    $isAtasan = ($userPeran === 'atasan');
    $isApprover = ($isAtasan || $isManajer);

    $kendaraanList = \App\Models\Kendaraan::all();
    $driverList = \App\Models\Driver::all();

    $pendingList = \App\Models\Pemesanan::with(['pegawai', 'kendaraan', 'driver', 'persetujuan.approver'])
        ->where('status', 'tertunda')
        ->latest()
        ->get();

    $riwayatList = \App\Models\Pemesanan::with(['pegawai', 'kendaraan', 'driver', 'persetujuan.approver'])
        ->latest()
        ->get();
@endphp

<!-- TOP HEADER -->
<header class="sticky top-0 z-40 bg-surface-container-lowest border-b border-outline-variant/50 px-4 lg:px-6 h-16 flex items-center justify-between shadow-xs">
    <div class="flex items-center gap-3">
        <button id="sidebarToggle" class="p-2 -ml-2 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors cursor-pointer" title="Toggle Navigasi" type="button">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
        <div class="flex items-center gap-2.5">
            <div class="rounded-xl overflow-hidden flex items-center justify-center w-12 h-12">
                <img src="{{ asset('images/logo.png') }}" alt="DriveControl Logo" class="w-full h-full object-contain">
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
                    <span class="text-xs font-bold text-on-surface">{{ session('user_nama') }}</span>
                    @php
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

<!-- BODY CONTAINER -->
<div class="flex-1 flex overflow-hidden">
    <!-- SIDEBAR -->
    <aside id="mainSidebar" class="flex-shrink-0 bg-surface-container-lowest border-r border-outline-variant/40 flex flex-col justify-between transition-all duration-300 w-64 z-40">
        <div class="p-4 space-y-6 overflow-y-auto">
            <div class="space-y-1">
                <p class="sidebar-text px-3 text-[11px] font-bold uppercase tracking-wider text-secondary/80 mb-2">Navigasi Utama</p>
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">dashboard</span><span class="sidebar-text truncate">Dashboard Utama</span>
                </a>
                <a href="{{ url('/pemesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary-container/10 text-primary font-semibold text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">assignment</span>
                    <span class="sidebar-text truncate">Pemesanan Kendaraan</span>
                    @if($pendingList->count() > 0)
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
                <a href="{{ url('/rekapitulasi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">file_download</span><span class="sidebar-text truncate">Ekspor Rekapitulasi</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl mx-auto w-full flex flex-col gap-6">
            
            <!-- ALERTS -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2 shadow-xs">
                    <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2 shadow-xs">
                    <span class="material-symbols-outlined text-rose-600 text-lg">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- PAGE TITLE & TAB SWITCHER -->
            <div class="flex flex-col gap-1">
                <h1 class="text-xl sm:text-2xl font-headline font-bold text-on-surface">Pemesanan Kendaraan</h1>

                <!-- TAB SWITCHER BUTTONS -->
                <div class="flex items-center gap-2 mt-3 border-b border-outline-variant/40">
                    <button class="flex items-center gap-2 pb-3 px-4 border-b-2 border-primary text-primary font-semibold text-xs cursor-pointer tab-btn active transition-all" onclick="switchTab('form')" id="btn-tab-form" type="button">
                        <span class="material-symbols-outlined text-base">edit_calendar</span>
                        <span>Formulir</span>
                    </button>
                    
                    <button class="flex items-center gap-2 pb-3 px-4 border-b-2 border-transparent text-secondary hover:text-on-surface font-semibold text-xs transition-all cursor-pointer tab-btn relative" onclick="switchTab('approval')" id="btn-tab-approval" type="button">
                        <span class="material-symbols-outlined text-base">verified_user</span>
                        <span>Persetujuan</span>
                        @if($pendingList->count() > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500 text-white font-bold">
                                {{ $pendingList->count() }}
                            </span>
                        @endif
                    </button>

                    <button class="flex items-center gap-2 pb-3 px-4 border-b-2 border-transparent text-secondary hover:text-on-surface font-semibold text-xs transition-all cursor-pointer tab-btn" onclick="switchTab('history')" id="btn-tab-history" type="button">
                        <span class="material-symbols-outlined text-base">history</span>
                        <span>Riwayat</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-surface-container text-secondary font-bold">
                            {{ $riwayatList->count() }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- BAGIAN TAB 1: FORMULIR PEMESANAN -->
            <div id="tab-form-content" class="tab-content flex flex-col gap-6">
                <form action="{{ url('/pemesanan/ajukan') }}" method="POST" class="flex flex-col gap-6" id="booking-form">
                    @csrf
                    <input type="hidden" name="pegawai_id" value="{{ session('user_id') }}">

                    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
                        <div class="flex items-center gap-2 border-b border-surface-container pb-3">
                            <span class="material-symbols-outlined text-primary text-xl">near_me</span>
                            <h2 class="font-headline font-semibold text-base text-on-surface">Lokasi & Tujuan</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface" for="lokasi_asal">Lokasi Asal</label>
                                <div class="relative">
                                    <select class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary appearance-none cursor-pointer" id="lokasi_asal" name="lokasi_asal">
                                        <option value="Site 01 - Pomalaa (Office Utama)">Site 01 - Pomalaa (Office Utama)</option>
                                        <option value="Mess Staff Pomalaa (Blok C)">Mess Staff Pomalaa (Blok C)</option>
                                        <option value="Pit West Area">Pit West Area</option>
                                        <option value="Stockpile 02 & Jetty">Stockpile 02 & Jetty</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-secondary text-lg">expand_more</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface" for="tujuan">Tujuan</label>
                                <div class="relative">
                                    <select class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary appearance-none cursor-pointer" id="tujuan" name="tujuan">
                                        <option value="Pit West Area (Tambang Nikel)">Pit West Area (Tambang Nikel)</option>
                                        <option value="Stockpile 02 & Jetty Port">Stockpile 02 & Jetty Port</option>
                                        <option value="Mess Staff Pomalaa">Mess Staff Pomalaa</option>
                                        <option value="Kantor Perwakilan Kendari">Kantor Perwakilan Kendari</option>
                                        <option value="Central Workshop & Maintenance">Central Workshop & Maintenance</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-secondary text-lg">expand_more</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
                        <div class="flex items-center gap-2 border-b border-surface-container pb-3">
                            <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
                            <h2 class="font-headline font-semibold text-base text-on-surface">Jadwal Penggunaan</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface" for="start-time">Waktu Mulai</label>
                                <input class="w-full h-11 px-3.5 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary" id="start-time" required type="datetime-local" name="waktu_mulai" value="{{ date('Y-m-d\TH:i', strtotime('+1 hour')) }}">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface" for="end-time">Waktu Selesai</label>
                                <input class="w-full h-11 px-3.5 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary" id="end-time" required type="datetime-local" name="waktu_selesai" value="{{ date('Y-m-d\TH:i', strtotime('+5 hours')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
                        <div class="flex items-center gap-2 border-b border-surface-container pb-3">
                            <span class="material-symbols-outlined text-primary text-xl">directions_car</span>
                            <h2 class="font-headline font-semibold text-base text-on-surface">Armada & Driver</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface" for="kendaraan_id">Unit Kendaraan</label>
                                <div class="relative">
                                    <select class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary appearance-none cursor-pointer" id="kendaraan_id" name="kendaraan_id">
                                        <option value="">-- Pilih Kendaraan --</option>
                                        @foreach($kendaraanList as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->plat_nomor }}) - {{ ucfirst($k->status) }}</option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-secondary text-lg">expand_more</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface" for="driver_id">Driver</label>
                                <div class="relative">
                                    <select class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary appearance-none cursor-pointer" id="driver_id" name="driver_id">
                                        <option value="">-- Pilih Driver --</option>
                                        @foreach($driverList as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama }} ({{ $d->telepon }})</option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-secondary text-lg">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-on-surface" for="keperluan">Keperluan</label>
                            <textarea class="w-full p-3 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary" id="keperluan" name="keperluan" rows="3" required placeholder="Tuliskan keperluan operasional..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ url('/dashboard') }}" class="px-6 h-11 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-highest text-xs font-semibold flex items-center justify-center transition-colors">Batal</a>
                        <button class="flex items-center gap-2 px-8 h-11 rounded-lg bg-primary text-on-primary hover:bg-primary-container text-xs font-semibold shadow-xs transition-all cursor-pointer" type="submit">
                            <span>Kirim Pengajuan</span>
                            <span class="material-symbols-outlined text-sm">send</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- BAGIAN TAB 2: PERSETUJUAN / APPROVAL -->
            <div id="tab-approval-content" class="tab-content flex flex-col gap-6 hidden">
                <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs flex flex-col gap-5 border border-outline-variant/40">
                    <div class="flex items-center justify-between border-b border-surface-container pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-xl">pending_actions</span>
                            <h2 class="font-headline font-semibold text-base text-on-surface">Menunggu Persetujuan</h2>
                        </div>
                        <span class="text-xs text-secondary font-semibold">{{ $pendingList->count() }} Permohonan</span>
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
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-outline-variant/30 pb-3">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <span class="px-2.5 py-1 rounded-lg bg-primary-container text-white font-bold text-xs">#REQ-{{ $item->id }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $lvl1Approved ? 'bg-sky-100 text-sky-800 border border-sky-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                            {{ $lvl1Approved ? 'Menunggu persetujuan atasan' : 'Menunggu persetujuan' }}
                                        </span>
                                        <span class="text-xs font-bold text-on-surface">{{ $item->pegawai->nama ?? 'Pegawai' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] text-secondary flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">schedule</span>
                                            {{ $item->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs bg-white/70 p-3.5 rounded-xl border border-outline-variant/20">
                                    <div>
                                        <span class="text-secondary text-[11px] block">Jadwal</span>
                                        <span class="font-semibold text-on-surface">{{ date('d M Y H:i', strtotime($item->waktu_mulai)) }} s/d {{ date('d M Y H:i', strtotime($item->waktu_selesai)) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-secondary text-[11px] block">Keperluan</span>
                                        <span class="font-semibold text-on-surface">{{ $item->keperluan }}</span>
                                    </div>
                                    <div>
                                        <span class="text-secondary text-[11px] block">Armada & Driver</span>
                                        <span class="font-semibold text-on-surface">
                                            {{ $item->kendaraan ? $item->kendaraan->nama . ' (' . $item->kendaraan->plat_nomor . ')' : 'Belum ditetapkan' }} / {{ $item->driver ? $item->driver->nama : 'Tanpa supir' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/30 flex flex-col gap-2">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="p-3 rounded-lg border {{ $lvl1Status === 'disetujui' ? 'bg-emerald-50 border-emerald-300' : ($lvl1Status === 'dilewati' ? 'bg-purple-50 border-purple-300' : ($lvl1Status === 'ditolak' ? 'bg-rose-50 border-rose-300' : 'bg-amber-50 border-amber-300')) }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1.5 font-bold text-xs">
                                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] {{ $lvl1Status === 'disetujui' ? 'bg-emerald-600 text-white' : ($lvl1Status === 'dilewati' ? 'bg-purple-600 text-white' : 'bg-amber-500 text-white') }}">1</span>
                                                    <span>Tahap 1 (Atasan)</span>
                                                </div>
                                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $lvl1Status === 'disetujui' ? 'bg-emerald-200 text-emerald-900' : ($lvl1Status === 'dilewati' ? 'bg-purple-200 text-purple-900' : ($lvl1Status === 'ditolak' ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900')) }}">
                                                    {{ $lvl1Status === 'dilewati' ? 'Dilewati' : ucfirst($lvl1Status) }}
                                                </span>
                                            </div>
                                            @if($lvl1 && $lvl1->catatan)
                                                <p class="text-[11px] text-secondary mt-1.5 italic">"{{ $lvl1->catatan }}"</p>
                                            @endif
                                        </div>

                                        <div class="p-3 rounded-lg border {{ $lvl2Status === 'disetujui' ? 'bg-emerald-50 border-emerald-300' : ($lvl2Status === 'ditolak' ? 'bg-rose-50 border-rose-300' : 'bg-amber-50 border-amber-300') }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1.5 font-bold text-xs">
                                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] {{ $lvl2Status === 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }}">2</span>
                                                    <span>Tahap 2 (Manajer)</span>
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

                                <div class="pt-2 flex flex-col gap-3">
                                    @if($isManajer)
                                        <div class="p-4 rounded-xl bg-purple-50/80 border border-purple-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                            <div>
                                                <div class="flex items-center gap-1.5 text-purple-900 font-bold text-xs">
                                                    <span class="material-symbols-outlined text-purple-700 text-base">verified</span>
                                                    <span>Otorisasi Manajer</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="ditolak">
                                                    <button class="px-3.5 py-2 rounded-lg bg-white text-rose-700 hover:bg-rose-50 border border-rose-200 font-semibold text-xs cursor-pointer" type="submit">Tolak</button>
                                                </form>
                                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="disetujui">
                                                    <button class="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer" type="submit">
                                                        <span>Setujui Langsung</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($isAdmin)
                                        <div class="p-3 rounded-xl bg-surface-container text-xs text-secondary italic">
                                            Akun Admin bertugas murni menginput pemesanan serta menetapkan armada dan driver. Otorisasi persetujuan berjenjang dilakukan oleh Atasan (Tahap 1) dan Manajer (Tahap 2).
                                        </div>
                                    @elseif($isAtasan)
                                        <div class="p-4 rounded-xl bg-amber-50/80 border-amber-200 border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                            <div>
                                                <div class="flex items-center gap-1.5 text-on-surface font-bold text-xs">
                                                    <span class="material-symbols-outlined text-amber-700 text-base">supervisor_account</span>
                                                    <span>Otorisasi Tahap 1 (Atasan Langsung)</span>
                                                </div>
                                                @if($lvl1Status === 'disetujui')
                                                    <p class="text-[11px] text-emerald-700 font-semibold mt-0.5">✓ Tahap 1 telah Anda setujui.</p>
                                                @endif
                                            </div>
                                            @if($lvl1Status === 'tertunda')
                                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="ditolak">
                                                    <button class="px-3.5 py-2 rounded-lg bg-white text-rose-700 hover:bg-rose-50 border border-rose-200 font-semibold text-xs cursor-pointer" type="submit">Tolak</button>
                                                </form>
                                                <form action="{{ url('/approval/'.$item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="disetujui">
                                                    <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs cursor-pointer" type="submit">
                                                        Setujui Tahap 1 (Atasan)
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-secondary text-xs">Tidak ada antrean persetujuan.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- BAGIAN TAB 3: RIWAYAT & MONITORING -->
            <div id="tab-history-content" class="tab-content flex flex-col gap-6 hidden">
                <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
                    <div class="flex items-center justify-between border-b border-surface-container pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-xl">history</span>
                            <h2 class="font-headline font-semibold text-base text-on-surface">Riwayat Pemesanan</h2>
                        </div>
                        <span class="text-xs text-secondary font-semibold">{{ $riwayatList->count() }} Data</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-outline-variant/40 text-secondary font-semibold uppercase text-[10px]">
                                    <th class="py-3 px-3">Pemesan</th>
                                    <th class="py-3 px-3">Jadwal</th>
                                    <th class="py-3 px-3">Keperluan</th>
                                    <th class="py-3 px-3">Armada & Driver</th>
                                    <th class="py-3 px-3 text-center">Tahap 1</th>
                                    <th class="py-3 px-3 text-center">Tahap 2</th>
                                    <th class="py-3 px-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                @forelse($riwayatList as $row)
                                    @php
                                        $s1 = $row->persetujuan->where('level', 1)->first();
                                        $s2 = $row->persetujuan->where('level', 2)->first();
                                    @endphp
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-primary">#REQ-{{ $row->id }}</div>
                                            <div class="text-[11px] text-on-surface">{{ $row->pegawai->nama ?? 'Pegawai' }}</div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <div class="font-medium text-on-surface">{{ date('d M Y, H:i', strtotime($row->waktu_mulai)) }}</div>
                                        </td>
                                        <td class="py-3 px-3 max-w-[200px]">
                                            <div class="truncate">{{ $row->keperluan }}</div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <div>{{ $row->kendaraan->nama ?? '-' }}</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-surface-container">
                                                {{ $s1?->status ?? 'Tertunda' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-surface-container">
                                                {{ $s2?->status ?? 'Tertunda' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-secondary text-xs">Belum ada riwayat permohonan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- JAVASCRIPT LOGIC -->
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-primary', 'text-primary');
            btn.classList.add('border-transparent', 'text-secondary');
        });

        const targetContent = document.getElementById('tab-' + tabName + '-content');
        const targetBtn = document.getElementById('btn-tab-' + tabName);

        if (targetContent) targetContent.classList.remove('hidden');
        if (targetBtn) {
            targetBtn.classList.add('border-primary', 'text-primary');
            targetBtn.classList.remove('border-transparent', 'text-secondary');
        }
    }

    // Penanganan Tab Aktif Menggunakan Blade Condition (Aman dari Error JS)
    document.addEventListener("DOMContentLoaded", () => {
        let activeTab = 'form';
        @if(request('tab') === 'approval' || session('tab') === 'approval' || ($isApprover && $pendingList->count() > 0 && !request('tab')))
            activeTab = 'approval';
        @elseif(request('tab') === 'history' || session('tab') === 'history')
            activeTab = 'history';
        @endif
        switchTab(activeTab);
    });

    // Sidebar Toggle
    const sidebar = document.getElementById('mainSidebar');
    let isCollapsed = false;
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        isCollapsed = !isCollapsed;
        sidebar.classList.toggle('w-64', !isCollapsed);
        sidebar.classList.toggle('w-20', isCollapsed);
        document.querySelectorAll('.sidebar-text').forEach(el => el.classList.toggle('hidden', isCollapsed));
    });
</script>

</body>
</html>
