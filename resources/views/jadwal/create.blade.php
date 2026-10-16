<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Tambah Jadwal - DriveControl</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        @layer base { html, body { margin: 0; padding: 0; overscroll-behavior: none; } }
        ::-webkit-scrollbar { display: none; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {"darkMode":"class","theme":{"extend":{"colors":{"primary-fixed-dim":"#b5c4ff","tertiary-fixed":"#cce5ff","outline":"#737687","surface-bright":"#f8f9ff","on-primary-container":"#eeefff","primary-container":"#1e60f6","error-container":"#ffdad6","inverse-primary":"#b5c4ff","on-secondary-fixed-variant":"#3f465c","tertiary-fixed-dim":"#93ccff","secondary":"#565e74","secondary-fixed-dim":"#bec6e0","outline-variant":"#c3c5d8","on-tertiary-container":"#e7f2ff","tertiary":"#005a89","on-primary":"#ffffff","inverse-on-surface":"#eaf1ff","on-secondary":"#ffffff","on-secondary-container":"#5c647a","surface-container-low":"#eff4ff","on-surface-variant":"#434655","tertiary-container":"#0073ae","surface-tint":"#0051e1","on-tertiary-fixed":"#001d31","surface-variant":"#d3e4fe","inverse-surface":"#213145","background":"#f8f9ff","primary":"#0048cc","surface-container-highest":"#d3e4fe","on-primary-fixed":"#00164d","surface-container-lowest":"#ffffff","surface-dim":"#cbdbf5","secondary-container":"#dae2fd","surface-container-high":"#dce9ff","primary-fixed":"#dce1ff","on-primary-fixed-variant":"#003cac","surface":"#f8f9ff","on-background":"#0b1c30","on-tertiary-fixed-variant":"#004b73","on-secondary-fixed":"#131b2e","surface-container":"#e5eeff","on-tertiary":"#ffffff","on-surface":"#0b1c30","secondary-fixed":"#dae2fd","error":"#ba1a1a","on-error":"#ffffff","on-error-container":"#93000a"},"borderRadius":{"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},"spacing":{"margin-mobile":"1.25rem","space-sm":"0.5rem","gutter":"1.5rem","margin":"2rem","gutter-mobile":"1rem","space-xs":"0.25rem","space-md":"1rem","space-xl":"2.5rem","space-lg":"1.5rem"},"fontFamily":{"label-md":["Inter"],"headline-xl":["Plus Jakarta Sans"],"body-sm":["Inter"],"body-lg":["Inter"],"headline-lg":["Plus Jakarta Sans"],"headline-md":["Plus Jakarta Sans"],"label-sm":["Inter"],"body-md":["Inter"],"headline-sm":["Plus Jakarta Sans"],"label-lg":["Inter"]}}}};</script>
</head>
<body class="bg-background font-body-md text-on-surface antialiased min-h-screen flex flex-col">

    <!-- TOP HEADER -->
    <header class="sticky top-0 z-40 bg-surface-container-lowest border-b border-outline-variant/50 px-4 lg:px-6 h-16 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3">
            <button id="sidebarToggle" class="p-2 -ml-2 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors cursor-pointer" title="Toggle Navigasi" onclick="toggleSidebar()">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="rounded-xl overflow-hidden flex items-center justify-center w-12 h-12">
                    <img src="{{ asset('images/logo.png') }}" alt="DriveControl Logo" class="w-full h-full object-contain">
                </div>
                <span class="font-headline font-bold tracking-tight text-lg bg-gradient-to-r from-primary to-on-surface bg-clip-text text-transparent">DriveControl</span>
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
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">dashboard</span><span class="sidebar-text truncate">Dashboard Utama</span>
                    </a>
                    <a href="{{ url('/pemesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">assignment</span><span class="sidebar-text truncate">Pemesanan Kendaraan</span>
                    </a>
                    <a href="{{ url('/armada') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low font-medium text-sm transition-colors">
                        <span class="material-symbols-outlined text-xl">directions_car</span><span class="sidebar-text truncate">Armada Tambang</span>
                    </a>
                    <a href="{{ url('/jadwal') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary-container/10 text-primary font-semibold text-sm transition-colors">
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
        <main class="flex-1 overflow-y-auto bg-background p-6 lg:p-8">
            <div class="max-w-2xl mx-auto flex flex-col gap-6 pb-12">
                
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm shadow-xs">
                        <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-secondary mb-1">
                            <a href="{{ route('jadwal.index') }}" class="hover:underline">Jadwal Driver & Servis</a>
                            <span>/</span>
                            <span>Tambah Jadwal</span>
                        </div>
                        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Tambah Penugasan Driver / Servis</h1>
                    </div>
                    <a href="{{ route('jadwal.index') }}" class="px-4 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant/40 text-on-surface text-xs font-semibold hover:bg-surface-container transition-colors">
                        Kembali
                    </a>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 p-6">
                    <form action="{{ route('jadwal.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-secondary mb-1">Kategori / Jenis Kegiatan</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-surface-container-low cursor-pointer">
                                    <input checked class="text-primary" name="kategori" type="radio" value="driver"/>
                                    <span class="text-xs font-semibold text-on-surface">Penugasan Driver</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl bg-surface-container-low cursor-pointer">
                                    <input class="text-primary" name="kategori" type="radio" value="servis"/>
                                    <span class="text-xs font-semibold text-on-surface">Servis Armada</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-secondary mb-1">Penanggung Jawab / Driver</label>
                                <input type="text" name="penanggung_jawab" required placeholder="Nama Driver / Bengkel..." class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-secondary mb-1">Jam / Waktu / Shift</label>
                                <input type="text" name="waktu" required placeholder="07:00 - 15:00 WITA" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-secondary mb-1">Judul / Unit Kendaraan</label>
                                <input type="text" name="judul" required placeholder="DT-01 Mitsubishi Fuso" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-secondary mb-1">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-secondary mb-1">Keterangan / Rute Tugas</label>
                            <input type="text" name="keterangan" placeholder="Contoh: Hauling Pit West ke Jetty Port" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-outline-variant/30">
                            <a href="{{ route('jadwal.index') }}" class="px-4 py-2 rounded-xl text-secondary hover:bg-surface-container font-semibold text-xs transition-colors">Batal</a>
                            <button class="px-5 py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-semibold text-xs transition-all shadow-sm cursor-pointer" type="submit">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        let isSidebarExpanded = true;
        function toggleSidebar() {
            isSidebarExpanded = !isSidebarExpanded;
            const sidebar = document.getElementById('mainSidebar');
            if (isSidebarExpanded) {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
            } else {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
            }
        }
    </script>
</body>
</html>
