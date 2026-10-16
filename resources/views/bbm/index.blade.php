<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsumsi BBM & Odo - DriveControl</title>
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
                        "tertiary-fixed": "#cce5ff",
                        "on-tertiary-fixed-variant": "#004b73",
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
                    <span class="text-[10px] px-1 py-0.2 rounded {{ $roleBadgeColor }} font-semibold uppercase">{{ session('user_peran') }}</span>
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

<!-- BODY CONTENT AREA (SIDEBAR + MAIN) -->
<div class="flex-1 flex overflow-hidden">
    <!-- SIDEBAR NAVIGASI -->
    <aside id="mainSidebar" class="flex-shrink-0 bg-surface-container-lowest border-r border-outline-variant/40 flex flex-col justify-between transition-all duration-300 w-64 z-40">
        <div class="p-4 space-y-6 overflow-y-auto">
            <div class="space-y-1">
                <p class="sidebar-text px-3 text-[11px] font-bold uppercase tracking-wider text-secondary/80 mb-2">Navigasi Utama</p>
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">dashboard</span><span class="sidebar-text truncate">Dashboard Utama</span>
                </a>
                <a href="{{ url('/pemesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">assignment</span>
                    <span class="sidebar-text truncate">Pemesanan Kendaraan</span>
                    @if(isset($pendingList) && $pendingList->count() > 0)
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
                <a href="{{ url('/bbm') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary-container text-on-primary font-semibold text-sm transition-colors shadow-sm">
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
        <div class="max-w-7xl mx-auto w-full flex flex-col gap-6 pb-12">
            
            <!-- Top Breadcrumb & Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-headline font-bold text-on-surface tracking-tight mt-1">Konsumsi BBM & Odometer</h1>
                </div>
            </div>

            <!-- Metric Summary Cards (Dinamis dari Database) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm border border-outline-variant/30 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary">Total BBM</span>
                        <span class="material-symbols-outlined text-xl text-primary">local_gas_station</span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-headline font-bold text-on-surface tracking-tight">{{ number_format($totalBbm ?? 0, 0, ',', '.') }}</span>
                        <span class="text-xs text-secondary font-medium">L</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm border border-outline-variant/30 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary">Rata-rata Konsumsi</span>
                        <span class="material-symbols-outlined text-xl text-tertiary">speed</span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-headline font-bold text-on-surface tracking-tight">{{ number_format($rataEfisiensi ?? 0, 1, ',', '.') }}</span>
                        <span class="text-xs text-secondary font-medium">km/L</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm border border-outline-variant/30 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary">Total Jarak</span>
                        <span class="material-symbols-outlined text-xl text-primary">directions_car</span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-headline font-bold text-on-surface tracking-tight">{{ number_format($totalJarak ?? 0, 0, ',', '.') }}</span>
                        <span class="text-xs text-secondary font-medium">km</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm border border-outline-variant/30 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary">Estimasi Biaya</span>
                        <span class="material-symbols-outlined text-xl text-secondary">payments</span>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-headline font-bold text-on-surface tracking-tight">Rp {{ number_format($totalBiayaJt ?? 0, 1, ',', '.') }}</span>
                        <span class="text-xs text-secondary font-medium">Jt</span>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Form -->
            <form method="GET" action="{{ url('/bbm') }}" class="bg-surface-container-lowest rounded-xl p-3 shadow-sm border border-outline-variant/30 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                    <button type="submit" name="kategori" value="Semua" class="px-4 py-2 rounded-lg {{ request('kategori', 'Semua') === 'Semua' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-secondary' }} font-semibold text-xs whitespace-nowrap shadow-sm cursor-pointer">Semua</button>
                    <button type="submit" name="kategori" value="LV" class="px-4 py-2 rounded-lg {{ request('kategori') === 'LV' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-secondary' }} font-semibold text-xs whitespace-nowrap cursor-pointer">LV</button>
                    <button type="submit" name="kategori" value="Truk" class="px-4 py-2 rounded-lg {{ request('kategori') === 'Truk' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-secondary' }} font-semibold text-xs whitespace-nowrap cursor-pointer">Truk</button>
                    <button type="submit" name="kategori" value="Bus" class="px-4 py-2 rounded-lg {{ request('kategori') === 'Bus' ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-secondary' }} font-semibold text-xs whitespace-nowrap cursor-pointer">Bus</button>
                </div>
                <div class="relative w-full md:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-secondary text-lg">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari armada / driver..." class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg text-xs text-on-surface placeholder:text-secondary focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/40 transition-all">
                </div>
            </form>

            <!-- Main Table Section (Looping Data dari Database) -->
            <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
                <div class="p-5 flex items-center justify-between border-b border-surface-container">
                    <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                        <h2 class="font-headline font-semibold text-sm text-on-surface">Log Konsumsi BBM & Odometer</h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-surface-container-low/60 text-secondary font-semibold uppercase tracking-wider">
                                <th class="py-3 px-5">No Lambung</th>
                                <th class="py-3 px-5">Driver</th>
                                <th class="py-3 px-5">Tanggal</th>
                                <th class="py-3 px-5">Odometer</th>
                                <th class="py-3 px-5">BBM</th>
                                <th class="py-3 px-5">Efisiensi</th>
                                <th class="py-3 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse($logs as $log)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center font-bold text-primary text-xs">{{ $log->jenis_kendaraan }}</div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-on-surface">{{ $log->no_lambung }}</span>
                                            <span class="text-[11px] text-secondary">Kendaraan Operasional</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-on-surface font-medium">{{ $log->driver }}</td>
                                <td class="py-4 px-5 text-secondary">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                <td class="py-4 px-5 font-semibold text-on-surface">{{ number_format($log->odometer, 0, ',', '.') }} km</td>
                                <td class="py-4 px-5"><span class="text-on-surface font-semibold">{{ $log->jumlah_bbm }} L</span> <span class="text-secondary text-[11px]">{{ $log->tipe_bbm }}</span></td>
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-on-surface">{{ $log->efisiensi }} km/L</span>
                                        @php
                                            $badgeBg = match($log->status_efisiensi) {
                                                'Efisien' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                                'Boros' => 'bg-error-container text-on-error-container',
                                                default => 'bg-secondary-container text-on-secondary-container'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeBg }}">{{ $log->status_efisiensi }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-right">
                                    <button class="text-primary hover:text-on-primary-fixed-variant transition-colors inline-flex items-center gap-1 font-semibold cursor-pointer" type="button">
                                        <span>Detail</span>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-secondary">Belum ada data log konsumsi BBM di database.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                @if($logs->hasPages())
                <div class="p-4 bg-surface-container-low/30 border-t border-surface-container">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>

        </div>
    </main>
</div>

<!-- Sidebar Toggle Script -->
<script>
    let isCollapsed = false;
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        isCollapsed = !isCollapsed;
        const sidebar = document.getElementById('mainSidebar');
        sidebar.classList.toggle('w-64', !isCollapsed);
        sidebar.classList.toggle('w-20', isCollapsed);
        document.querySelectorAll('.sidebar-text').forEach(el => el.classList.toggle('hidden', isCollapsed));
    });
</script>

</body>
</html>