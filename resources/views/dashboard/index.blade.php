<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveControl</title>
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
        <button id="sidebarToggle" class="p-2 -ml-2 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Toggle Navigasi">
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
                    <span class="text-[10px] px-1 py-0.2 rounded bg-emerald-100 text-emerald-800 font-semibold uppercase">{{ session('user_peran') }}</span>
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
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary-container/10 text-primary font-semibold text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">dashboard</span><span class="sidebar-text truncate">Dashboard Utama</span>
                </a>
                <a href="{{ url('/pemesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined text-xl">assignment</span><span class="sidebar-text truncate">Pemesanan Kendaraan</span>
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

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif

        <!-- HERO BANNER -->
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary to-primary-container text-white p-6 sm:p-8 shadow-sm">
            <div class="relative z-10 max-w-2xl">
                <h1 class="text-2xl sm:text-3xl font-headline font-bold tracking-tight">Selamat Datang, {{ session('user_nama') }}</h1>
                <p class="mt-2 text-sm sm:text-base text-white/80 leading-relaxed">Sistem siap mengelola pemesanan dan perizinan armada.</p>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <button onclick="toggleModal(true)" class="px-4 py-2.5 rounded-lg bg-white text-primary font-semibold text-xs sm:text-sm hover:bg-surface-container-low transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">add_circle</span> Pesan Kendaraan Operasional
                    </button>
                    <a href="{{ url('/pemesanan') }}" class="px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-medium text-xs sm:text-sm backdrop-blur-md transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">fact_check</span> Kelola Pemesanan & Approval
                    </a>
                </div>
            </div>
            <div class="absolute -right-8 -bottom-8 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-12 top-6 opacity-10 pointer-events-none hidden lg:block"><span class="material-symbols-outlined text-[160px]">local_shipping</span></div>
        </section>

        <!-- STATS CARDS -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['Need Approval', \App\Models\Pemesanan::where('status', 'tertunda')->count(), 'pending', 'amber', 'Menunggu otorisasi', 'approval_delegation'],
                ['Total Pemesanan', \App\Models\Pemesanan::count(), 'near_me', 'blue', 'Keseluruhan data', 'directions_car'],
                ['Armada Tersedia', \App\Models\Kendaraan::where('status', 'tersedia')->count(), 'check_circle', 'emerald', 'Siap mobilisasi', 'commute'],
                ['Driver Siap', \App\Models\Driver::where('status', 'tersedia')->count(), 'badge', 'rose', 'Aktif bertugas', 'car_repair']
            ] as [$title, $count, $icon, $color, $desc, $cardIcon])
            <div class="bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-secondary">{{ $title }}</p>
                    <h3 class="text-2xl font-headline font-bold text-on-surface mt-1">{{ $count }}</h3>
                    <p class="text-[11px] text-{{ $color }}-600 font-medium mt-1 flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-xs">{{ $icon }}</span> {{ $desc }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">{{ $cardIcon }}</span>
                </div>
            </div>
            @endforeach
        </section>

        <!-- TABLE & DISTRIBUTION GRID -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- RECENT BOOKINGS -->
            <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-headline font-semibold text-base text-on-surface">Daftar Pemesanan Kendaraan Terbaru</h3>
                            <p class="text-xs text-secondary">Status validasi dan penugasan armada operasional</p>
                        </div>
                        <a href="{{ url('/pemesanan') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                            Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-outline-variant/30 text-secondary font-semibold uppercase tracking-wider text-[10px]">
                                    <th class="py-2.5 px-3">ID / Pemesan</th>
                                    <th class="py-2.5 px-3">Keperluan</th>
                                    <th class="py-2.5 px-3">Jadwal Mulai</th>
                                    <th class="py-2.5 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                @forelse(\App\Models\Pemesanan::with(['pegawai', 'persetujuan'])->latest()->take(5)->get() as $b)
                                    <tr class="hover:bg-surface-container-low/40 transition-colors">
                                        <td class="py-3 px-3"><div class="font-bold text-on-surface">#{{ $b->id }}</div><div class="text-[11px] text-secondary">{{ $b->pegawai->nama ?? 'Pegawai' }}</div></td>
                                        <td class="py-3 px-3"><div class="font-medium text-on-surface">{{ $b->keperluan }}</div></td>
                                        <td class="py-3 px-3"><div class="font-medium">{{ $b->waktu_mulai }}</div></td>
                                        <td class="py-3 px-3">
                                            @php 
                                                $s1 = $b->persetujuan->where('level', 1)->first();
                                                $statusLabel = match($b->status) {
                                                    'disetujui' => 'Disetujui',
                                                    'ditolak' => 'Ditolak',
                                                    default => ($s1 && ($s1->status === 'disetujui' || $s1->status === 'dilewati')) ? 'Menunggu persetujuan atasan' : 'Menunggu persetujuan'
                                                };
                                                $c = match($b->status) {
                                                    'disetujui' => 'emerald',
                                                    'ditolak' => 'rose',
                                                    default => ($s1 && ($s1->status === 'disetujui' || $s1->status === 'dilewati')) ? 'sky' : 'amber'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $c }}-100 text-{{ $c }}-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-{{ $c }}-600"></span> {{ $statusLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-6 text-secondary text-xs">Belum ada data pemesanan kendaraan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- FLEET DISTRIBUTION -->
            <div class="bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-headline font-semibold text-base text-on-surface">Distribusi Armada</h3>
                        <span class="material-symbols-outlined text-secondary text-lg">pie_chart</span>
                    </div>

                    <div class="space-y-3 mt-2 text-xs">
                        @foreach([
                            ['Ready Unit (Tersedia)', \App\Models\Kendaraan::where('status', 'tersedia')->count(), 'emerald'],
                            ['Need Approval', \App\Models\Pemesanan::where('status', 'tertunda')->count(), 'amber'],
                            ['Maintenance (Servis)', \App\Models\Kendaraan::where('status', 'maintenance')->count(), 'rose']
                        ] as [$label, $val, $col])
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-surface-container-low/50">
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-{{ $col }}-500"></span><span class="font-medium text-on-surface">{{ $label }}</span></div>
                            <span class="font-bold text-on-surface">{{ $val }} Unit</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- BOOKING MODAL -->
<div id="bookingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs hidden p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-outline-variant/40">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center"><span class="material-symbols-outlined text-xl">car_rental</span></div>
                <div>
                    <h3 class="font-headline font-bold text-base text-on-surface">Form Pemesanan Kendaraan</h3>
                    <p class="text-[11px] text-secondary">Pengajuan izin mobilisasi operasional</p>
                </div>
            </div>
            <button onclick="toggleModal(false)" class="p-1 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container cursor-pointer"><span class="material-symbols-outlined text-xl">close</span></button>
        </div>

        <form action="{{ url('/pemesanan/ajukan') }}" method="POST" class="space-y-4 mt-4">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ session('user_id') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-on-surface mb-1">Waktu Mulai Tugas</label>
                    <input type="datetime-local" name="waktu_mulai" required class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-on-surface mb-1">Waktu Selesai</label>
                    <input type="datetime-local" name="waktu_selesai" required class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface mb-1">Keperluan Tugas / Penugasan Khusus</label>
                <textarea name="keperluan" rows="3" required placeholder="Tuliskan rincian tugas..." class="w-full text-xs rounded-lg border border-outline-variant px-3 py-2 bg-surface-container-low focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
            </div>
            <div class="pt-2 flex items-center justify-end gap-2 border-t border-outline-variant/30">
                <button type="button" onclick="toggleModal(false)" class="px-4 py-2 rounded-lg text-xs font-semibold text-secondary hover:bg-surface-container cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold bg-primary hover:bg-primary-container text-white shadow-xs cursor-pointer">Kirim Permohonan</button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT -->
<script>
    const sidebar = document.getElementById('mainSidebar'), modal = document.getElementById('bookingModal');
    let isCollapsed = false;

    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        isCollapsed = !isCollapsed;
        sidebar.classList.toggle('w-64', !isCollapsed);
        sidebar.classList.toggle('w-20', isCollapsed);
        document.querySelectorAll('.sidebar-text').forEach(el => el.classList.toggle('hidden', isCollapsed));
    });

    function toggleModal(show) {
        modal.classList.toggle('hidden', !show);
    }

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleModal(false); });
</script>

</body>
</html>