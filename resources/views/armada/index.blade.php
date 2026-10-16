<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armada Tambang - DriveControl</title>
    <style>
        @layer base { html, body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; } }
    </style>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              colors: {
                "primary": "#0048cc", "primary-container": "#1e60f6", "on-primary": "#ffffff",
                "secondary": "#565e74", "secondary-container": "#dae2fd",
                "background": "#f8f9ff", "surface": "#f8f9ff", "surface-container-lowest": "#ffffff",
                "surface-container-low": "#eff4ff", "surface-container": "#e5eeff", "surface-container-high": "#dce9ff",
                "on-surface": "#0b1c30", "outline": "#737687", "outline-variant": "#c3c5d8", "error": "#ba1a1a"
              },
              fontFamily: { sans: ["Inter", "sans-serif"], headline: ["Plus Jakarta Sans", "sans-serif"] }
            }
          }
        }
    </script>
</head>
<body class="bg-background text-on-surface min-h-screen flex flex-col font-sans antialiased">

<!-- TOP HEADER -->
<header class="sticky top-0 z-40 bg-surface-container-lowest border-b border-outline-variant/50 px-4 lg:px-6 h-16 flex items-center justify-between shadow-xs">
    <div class="flex items-center gap-3">
        <button id="sidebarToggle" class="p-2 -ml-2 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors cursor-pointer" title="Toggle Navigasi">
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
        <button class="md:hidden p-2 rounded-lg text-secondary hover:bg-surface-container-low" title="Pilih Site"><span class="material-symbols-outlined text-xl">location_on</span></button>
        @include('partials.header-notification')
        <div class="h-6 w-px bg-outline-variant/60 mx-1 hidden sm:block"></div>

        <div class="flex items-center gap-2.5 pl-1">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-primary to-primary-container text-white font-bold flex items-center justify-center text-xs shadow-xs">
                {{ strtoupper(substr(session('user_nama', 'User'), 0, 2)) }}
            </div>
            <div class="hidden xl:flex flex-col text-left">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-on-surface">{{ session('user_nama') }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold uppercase">{{ session('user_peran') }}</span>
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
                <a href="{{ url('/pemesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">assignment</span><span class="sidebar-text truncate">Pemesanan Kendaraan</span>
                </a>
                <a href="{{ url('/armada') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary-container/10 text-primary font-semibold text-sm transition-colors">
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

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2 shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2 shadow-xs">
                <span class="material-symbols-outlined text-red-600 text-lg">error</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- PAGE HEADER & ACTION -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <nav class="flex items-center gap-1.5 text-secondary text-xs">
                </nav>
                <h1 class="text-2xl font-headline font-bold text-on-surface tracking-tight mt-1">Armada Tambang</h1>
                <p class="text-xs sm:text-sm text-secondary">Manajemen unit operasional, status, dan penambahan armada baru</p>
            </div>
            <div>
                <button onclick="toggleAddModal(true)" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary hover:bg-primary-container shadow-sm transition-all font-semibold text-xs cursor-pointer" type="button">
                    <span class="material-symbols-outlined text-lg">add</span>
                    <span>Tambah Unit</span>
                </button>
            </div>
        </div>

        <!-- METRIC SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-secondary">Total Armada</p>
                    <h3 class="text-2xl font-headline font-bold text-on-surface mt-1">{{\App\Models\Kendaraan::count()}}</h3>
                    <p class="text-[11px] text-secondary font-medium mt-1">Keseluruhan unit terdaftar</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">local_shipping</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-secondary">Siap Pakai</p>
                    <h3 class="text-2xl font-headline font-bold text-emerald-600 mt-1">{{\App\Models\Kendaraan::where('status', 'tersedia')->count()}}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-1">Tersedia di pool</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-secondary">Sedang Bertugas</p>
                    <h3 class="text-2xl font-headline font-bold text-primary mt-1">{{\App\Models\Kendaraan::where('status', 'digunakan')->count()}}</h3>
                    <p class="text-[11px] text-primary font-medium mt-1">Operasional lapangan</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">commute</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-secondary">Dalam Perawatan</p>
                    <h3 class="text-2xl font-headline font-bold text-amber-600 mt-1">{{\App\Models\Kendaraan::where('status', 'maintenance')->count()}}</h3>
                    <p class="text-[11px] text-amber-600 font-medium mt-1">Maintenance / servis</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">car_repair</span>
                </div>
            </div>
        </div>

        <!-- FILTER & SEARCH BAR -->
        <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-xs border border-outline-variant/40 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0" id="fleet-category-tabs">
                <button class="category-pill px-3.5 py-1.5 rounded-lg font-semibold text-xs bg-primary-container text-on-primary transition-all whitespace-nowrap shadow-xs cursor-pointer" data-category="all" type="button">Semua</button>
                <button class="category-pill px-3.5 py-1.5 rounded-lg font-semibold text-xs text-secondary hover:bg-surface-container-low transition-all whitespace-nowrap cursor-pointer" data-category="lv" type="button">LV Penumpang</button>
                <button class="category-pill px-3.5 py-1.5 rounded-lg font-semibold text-xs text-secondary hover:bg-surface-container-low transition-all whitespace-nowrap cursor-pointer" data-category="truck" type="button">Truk Logistik</button>
                <button class="category-pill px-3.5 py-1.5 rounded-lg font-semibold text-xs text-secondary hover:bg-surface-container-low transition-all whitespace-nowrap cursor-pointer" data-category="bus" type="button">Crew Bus</button>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 sm:w-72">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-secondary">search</span>
                    <input class="w-full pl-9 pr-3 py-2 text-on-surface text-xs bg-surface-container-low rounded-xl outline-none focus:bg-white focus:ring-1 focus:ring-primary placeholder:text-secondary/70 transition-all border border-outline-variant/40" id="fleet-search-input" placeholder="Cari armada..." type="text">
                </div>
                <div class="relative">
                    <select class="appearance-none pl-3 pr-8 py-2 font-semibold text-xs text-on-surface bg-surface-container-low rounded-xl outline-none focus:ring-1 focus:ring-primary cursor-pointer transition-all border border-outline-variant/40" id="fleet-status-filter">
                        <option value="all">Semua Status</option>
                        <option value="ready">Siap Pakai</option>
                        <option value="on-duty">Operasional</option>
                        <option value="maintenance">Perawatan</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-secondary pointer-events-none">expand_more</span>
                </div>
            </div>
        </div>

        <!-- MAIN FLEET TABLE -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/40 overflow-hidden flex flex-col">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse" id="fleet-table">
                    <thead>
                        <tr class="bg-surface-container-low text-secondary text-[11px] uppercase tracking-wider border-b border-outline-variant/30 font-bold">
                            <th class="py-3.5 px-6">No Lambung & Model Unit</th>
                            <th class="py-3.5 px-4">Pelat Nomor</th>
                            <th class="py-3.5 px-4">Lokasi / Wilayah</th>
                            <th class="py-3.5 px-4">Driver Bertugas</th>
                            <th class="py-3.5 px-4">Status Unit</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container text-xs text-on-surface">
                        @php
                            $armadaList = $armadaList ?? \App\Models\Kendaraan::with('wilayah')->latest()->get();
                        @endphp

                        @forelse($armadaList as $armada)
                            @php
                                $cat = 'lv';
                                if(stripos($armada->model, 'truk') !== false || stripos($armada->model, 'dump') !== false || stripos($armada->merek, 'fuso') !== false) {
                                    $cat = 'truck';
                                } elseif(stripos($armada->model, 'hiace') !== false || stripos($armada->model, 'bus') !== false) {
                                    $cat = 'bus';
                                }

                                $st = 'ready';
                                if($armada->status == 'digunakan') $st = 'on-duty';
                                if($armada->status == 'maintenance') $st = 'maintenance';
                            @endphp
                            <tr class="hover:bg-surface-container-low/50 transition-colors group" data-category="{{ $cat }}" data-status="{{ $st }}">
                                <td class="py-3.5 px-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-on-surface">#{{ $armada->id }} - {{ $armada->merek ?: $armada->nama }}</span>
                                        <span class="text-[11px] text-secondary">{{ $armada->model ?: ($armada->merek ? $armada->nama : 'Operasional Tambang') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-on-surface">{{ $armada->plat_nomor }}</td>
                                <td class="py-3.5 px-4 text-on-surface">Pool Site 01 Pomalaa</td>
                                <td class="py-3.5 px-4 text-secondary">
                                    @if($armada->status == 'digunakan')
                                        Bertugas Lapangan
                                    @else
                                        Standby Pool
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold
                                        @if($armada->status == 'tersedia') bg-emerald-50 text-emerald-700
                                        @elseif($armada->status == 'digunakan') bg-blue-50 text-primary
                                        @else bg-amber-50 text-amber-700 @endif">
                                        <span class="w-1.5 h-1.5 rounded-full 
                                            @if($armada->status == 'tersedia') bg-emerald-500
                                            @elseif($armada->status == 'digunakan') bg-primary animate-pulse
                                            @else bg-amber-500 @endif"></span>
                                        {{ ucfirst($armada->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <button onclick="openDetailModal({{ json_encode([
                                        'id' => $armada->id,
                                        'nama' => $armada->nama,
                                        'merek' => $armada->merek,
                                        'model' => $armada->model,
                                        'plat_nomor' => $armada->plat_nomor,
                                        'status' => $armada->status,
                                        'jenis' => $armada->jenis,
                                        'kepemilikan' => $armada->kepemilikan,
                                        'kapasitas' => $armada->kapasitas,
                                        'wilayah_nama' => $armada->wilayah?->nama ?? 'Pool Site 01 Pomalaa',
                                        'created_at' => $armada->created_at?->format('d M Y H:i') ?? '-'
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }})" class="px-2.5 py-1 rounded-lg text-primary font-semibold hover:bg-surface-container-high transition-colors cursor-pointer" type="button">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-secondary text-xs">Belum ada data armada tambang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TABLE FOOTER -->
            <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-surface-container-lowest gap-4 border-t border-outline-variant/30 text-xs">
                <span class="text-secondary">
                    Menampilkan <span class="font-semibold text-on-surface" id="visible-count">{{ count($armadaList) }}</span> unit armada
                </span>
                <div class="flex items-center gap-2">
                    <button class="inline-flex items-center justify-center p-2 rounded-lg bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface disabled:opacity-40 transition-colors" disabled type="button">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="w-8 h-8 rounded-lg bg-primary text-on-primary font-semibold flex items-center justify-center" type="button">1</button>
                    </div>
                    <button class="inline-flex items-center justify-center p-2 rounded-lg bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface transition-colors" disabled type="button">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- MODAL TAMBAH UNIT -->
<div id="addUnitModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs hidden p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full p-6 shadow-2xl border border-outline-variant/40">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">local_shipping</span>
                </div>
                <div>
                    <h3 class="font-headline font-bold text-sm text-on-surface">Tambah Unit Armada Baru</h3>
                    <p class="text-[11px] text-secondary">Input data unit operasional tambang</p>
                </div>
            </div>
            <button onclick="toggleAddModal(false)" class="p-1 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container cursor-pointer">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form action="{{ url('/armada/store') }}" method="POST" class="space-y-4 mt-4">
            @csrf
            <div>
                <label class="block font-semibold text-xs text-on-surface mb-1">Merek / Pabrikan</label>
                <input type="text" name="merek" required placeholder="Contoh: Toyota, Mitsubishi, Hino..." class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block font-semibold text-xs text-on-surface mb-1">Model / Tipe Unit</label>
                <input type="text" name="model" required placeholder="Contoh: Hilux Double Cabin 2.4G" class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block font-semibold text-xs text-on-surface mb-1">Pelat Nomor / No Lambung</label>
                <input type="text" name="plat_nomor" required placeholder="Contoh: DT 9901 IMU" class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary font-mono">
            </div>
            <div>
                <label class="block font-semibold text-xs text-on-surface mb-1">Status Awal Unit</label>
                <select name="status" required class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer">
                    <option value="tersedia">Siap Pakai (Tersedia)</option>
                    <option value="digunakan">Sedang Digunakan (Operasional)</option>
                    <option value="maintenance">Dalam Perawatan (Maintenance)</option>
                </select>
            </div>
            <div class="pt-2 flex items-center justify-end gap-2 border-t border-outline-variant/30">
                <button type="button" onclick="toggleAddModal(false)" class="px-4 py-2 rounded-lg text-xs font-semibold text-secondary hover:bg-surface-container cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold bg-primary hover:bg-primary-container text-white shadow-xs cursor-pointer">Simpan Unit Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL & KELOLA ARMADA -->
<div id="detailUnitModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs hidden p-4" onclick="if(event.target === this) toggleDetailModal(false)">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-outline-variant/40 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">directions_car</span>
                </div>
                <div>
                    <h3 class="font-headline font-bold text-sm text-on-surface" id="detailModalTitle">Detail Unit Armada</h3>
                    <p class="text-[11px] text-secondary">Informasi spesifikasi, perbarui data, atau kelola unit</p>
                </div>
            </div>
            <button onclick="toggleDetailModal(false)" class="p-1 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container cursor-pointer" type="button">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="detailEditForm" method="POST" action="" class="space-y-4 mt-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="detailId" name="id" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-xs text-on-surface mb-1">Merek / Pabrikan</label>
                    <input type="text" id="detailMerek" name="merek" required class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block font-semibold text-xs text-on-surface mb-1">Model / Tipe Unit</label>
                    <input type="text" id="detailModel" name="model" required class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-xs text-on-surface mb-1">Pelat Nomor / No Lambung</label>
                    <input type="text" id="detailPlatNomor" name="plat_nomor" required class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary font-mono uppercase">
                </div>
                <div>
                    <label class="block font-semibold text-xs text-on-surface mb-1">Status Operasional</label>
                    <select id="detailStatus" name="status" required class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer">
                        <option value="tersedia">Siap Pakai (Tersedia)</option>
                        <option value="digunakan">Sedang Digunakan (Operasional)</option>
                        <option value="maintenance">Dalam Perawatan (Maintenance)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-xs text-on-surface mb-1">Jenis Kendaraan</label>
                    <select id="detailJenis" name="jenis" class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer">
                        <option value="penumpang">Penumpang</option>
                        <option value="barang">Barang / Angkutan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-xs text-on-surface mb-1">Kepemilikan</label>
                    <select id="detailKepemilikan" name="kepemilikan" class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer">
                        <option value="milik_perusahaan">Milik Sendiri</option>
                        <option value="sewa">Sewa / Rental</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-xs text-on-surface mb-1">Kapasitas</label>
                    <input type="number" id="detailKapasitas" name="kapasitas" min="1" class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <!-- Ringkasan Info Tambahan -->
            <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/30 text-[11px] space-y-1">
                <div class="flex justify-between text-secondary">
                    <span>Wilayah Penugasan:</span>
                    <span class="font-semibold text-on-surface" id="detailWilayah">Pool Site 01 Pomalaa</span>
                </div>
                <div class="flex justify-between text-secondary">
                    <span>Terdaftar Pada:</span>
                    <span class="font-semibold text-on-surface" id="detailCreatedAt">-</span>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-between gap-2 border-t border-outline-variant/30">
                <button type="button" id="btnHapusArmada" onclick="confirmDeleteArmada()" class="px-3 py-2 rounded-lg text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors flex items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-base">delete</span>
                    <span>Hapus Unit</span>
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleDetailModal(false)" class="px-4 py-2 rounded-lg text-xs font-semibold text-secondary hover:bg-surface-container cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold bg-primary hover:bg-primary-container text-white shadow-xs cursor-pointer flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">save</span>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>

        <form id="deleteArmadaForm" method="POST" action="" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- JAVASCRIPT -->
<script>
    const sidebar = document.getElementById('mainSidebar');
    let isCollapsed = false;

    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        isCollapsed = !isCollapsed;
        sidebar.classList.toggle('w-64', !isCollapsed);
        sidebar.classList.toggle('w-20', isCollapsed);
        document.querySelectorAll('.sidebar-text').forEach(el => el.classList.toggle('hidden', isCollapsed));
    });

    function toggleAddModal(show) {
        document.getElementById('addUnitModal').classList.toggle('hidden', !show);
    }

    function toggleDetailModal(show) {
        const modal = document.getElementById('detailUnitModal');
        if (modal) modal.classList.toggle('hidden', !show);
    }

    function openDetailModal(data) {
        if (!data) return;
        
        const updateUrl = "{{ url('/armada') }}/" + data.id;
        document.getElementById('detailEditForm').action = updateUrl;
        document.getElementById('deleteArmadaForm').action = updateUrl;
        
        document.getElementById('detailId').value = data.id || '';
        document.getElementById('detailModalTitle').textContent = 'Detail Armada #' + data.id + ' - ' + (data.merek || '') + ' ' + (data.model || '');
        document.getElementById('detailMerek').value = data.merek || '';
        document.getElementById('detailModel').value = data.model || '';
        document.getElementById('detailPlatNomor').value = data.plat_nomor || '';
        document.getElementById('detailStatus').value = data.status || 'tersedia';
        document.getElementById('detailJenis').value = data.jenis || 'penumpang';
        document.getElementById('detailKepemilikan').value = data.kepemilikan || 'milik_perusahaan';
        document.getElementById('detailKapasitas').value = data.kapasitas || 4;
        
        const wilayahEl = document.getElementById('detailWilayah');
        if (wilayahEl) {
            wilayahEl.textContent = data.wilayah_nama || (data.wilayah && data.wilayah.nama ? data.wilayah.nama : 'Pool Site 01 Pomalaa');
        }
        
        const createdEl = document.getElementById('detailCreatedAt');
        if (createdEl) {
            createdEl.textContent = data.created_at || '-';
        }
        
        const btnHapus = document.getElementById('btnHapusArmada');
        if (btnHapus) {
            if (data.status === 'digunakan') {
                btnHapus.title = 'Armada sedang digunakan dan tidak dapat dihapus.';
                btnHapus.classList.add('opacity-50');
            } else {
                btnHapus.title = 'Hapus unit armada ini';
                btnHapus.classList.remove('opacity-50');
            }
        }
        
        toggleDetailModal(true);
    }

    function confirmDeleteArmada() {
        const st = document.getElementById('detailStatus').value;
        if (st === 'digunakan') {
            alert('Armada sedang berstatus operasional (digunakan) dan tidak dapat dihapus.');
            return;
        }
        if (confirm('Apakah Anda yakin ingin menghapus unit armada ini dari database?')) {
            document.getElementById('deleteArmadaForm').submit();
        }
    }

    document.addEventListener('keydown', (e) => { 
        if (e.key === 'Escape') {
            toggleAddModal(false);
            toggleDetailModal(false);
        }
    });

    (function initFleetFilter() {
        const searchInput = document.getElementById('fleet-search-input');
        const statusSelect = document.getElementById('fleet-status-filter');
        const categoryButtons = document.querySelectorAll('.category-pill');
        const tableRows = document.querySelectorAll('#fleet-table tbody tr');
        const visibleCountEl = document.getElementById('visible-count');

        let currentCategory = 'all';

        function applyFilters() {
            const query = (searchInput.value || '').toLowerCase().trim();
            const status = statusSelect.value;
            let count = 0;

            tableRows.forEach(row => {
                const rowCategory = row.getAttribute('data-category');
                const rowStatus = row.getAttribute('data-status');
                const text = row.innerText.toLowerCase();

                const matchesQuery = query === '' || text.includes(query);
                const matchesCategory = currentCategory === 'all' || rowCategory === currentCategory;
                const matchesStatus = status === 'all' || rowStatus === status;

                if (matchesQuery && matchesCategory && matchesStatus) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCountEl) {
                visibleCountEl.textContent = count;
            }
        }

        categoryButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                categoryButtons.forEach(b => {
                    b.classList.remove('bg-primary-container', 'text-on-primary', 'shadow-xs');
                    b.classList.add('text-secondary', 'hover:bg-surface-container-low');
                });
                btn.classList.add('bg-primary-container', 'text-on-primary', 'shadow-xs');
                btn.classList.remove('text-secondary', 'hover:bg-surface-container-low');

                currentCategory = btn.getAttribute('data-category');
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', applyFilters);
        }
    })();
</script>

</body>
</html>