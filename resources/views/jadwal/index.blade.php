<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Jadwal Driver & Servis - DriveControl</title>
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
            <button class="md:hidden p-2 rounded-lg text-secondary hover:bg-surface-container-low cursor-pointer" title="Pilih Site"><span class="material-symbols-outlined text-xl">location_on</span></button>
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
            <div class="max-w-7xl mx-auto flex flex-col gap-6 pb-12">

                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm shadow-xs">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm shadow-xs">
                        <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <!-- Header Action Bar -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-secondary mb-1">
                        </div>
                        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Jadwal Driver & Servis</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="flex items-center gap-2 px-4 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant/40 text-on-surface text-sm font-semibold shadow-xs cursor-pointer" type="button">
                            <span class="material-symbols-outlined text-base text-secondary">calendar_month</span><span>{{ date('d M Y') }}</span>
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-container transition-all text-sm font-semibold shadow-sm cursor-pointer active:scale-95" onclick="toggleModal(true)" type="button">
                            <span class="material-symbols-outlined text-lg">add</span><span>Tambah Jadwal</span>
                        </button>
                    </div>
                </div>

                <!-- KPI Cards (DINAMIS DENGAN PERHITUNGAN COLLECTION LARAVEL) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-5 bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 flex items-center justify-between">
                        <div><span class="text-xs font-medium text-secondary">Total Jadwal</span><h3 class="text-2xl font-bold text-on-surface mt-1">{{ count($jadwals ?? []) }}</h3></div>
                        <div class="w-12 h-12 rounded-xl bg-primary-container/20 text-primary flex items-center justify-center"><span class="material-symbols-outlined text-2xl">badge</span></div>
                    </div>
                    <div class="p-5 bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 flex items-center justify-between">
                        <div><span class="text-xs font-medium text-secondary">Driver Bertugas</span><h3 class="text-2xl font-bold text-on-surface mt-1">{{ isset($jadwals) ? $jadwals->where('kategori', 'driver')->count() : 0 }}</h3></div>
                        <div class="w-12 h-12 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined text-2xl">airline_seat_recline_normal</span></div>
                    </div>
                    <div class="p-5 bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 flex items-center justify-between">
                        <div><span class="text-xs font-medium text-secondary">Servis Terjadwal</span><h3 class="text-2xl font-bold text-on-surface mt-1">{{ isset($jadwals) ? $jadwals->where('kategori', 'servis')->count() : 0 }}</h3></div>
                        <div class="w-12 h-12 rounded-xl bg-tertiary-fixed text-tertiary flex items-center justify-center"><span class="material-symbols-outlined text-2xl">build_circle</span></div>
                    </div>
                    <div class="p-5 bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 flex items-center justify-between">
                        <div><span class="text-xs font-medium text-secondary">Status Sistem</span><h3 class="text-2xl font-bold text-primary mt-1">Aktif</h3></div>
                        <div class="w-12 h-12 rounded-xl bg-surface-container-low text-primary flex items-center justify-center"><span class="material-symbols-outlined text-2xl">verified</span></div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-outline-variant/30 overflow-hidden">
                    <div class="p-5 border-b border-outline-variant/30 flex items-center justify-between">
                        <h3 class="font-bold text-on-surface text-base">Daftar Penugasan Driver & Servis</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low/70 text-secondary text-xs uppercase tracking-wider border-b border-outline-variant/30">
                                    <th class="py-3.5 px-6 font-semibold">Penanggung Jawab / Driver</th>
                                    <th class="py-3.5 px-4 font-semibold">Waktu / Shift</th>
                                    <th class="py-3.5 px-4 font-semibold">Unit / Judul</th>
                                    <th class="py-3.5 px-4 font-semibold">Tanggal</th>
                                    <th class="py-3.5 px-4 font-semibold">Kategori</th>
                                    <th class="py-3.5 px-4 font-semibold">Keterangan</th>
                                    <th class="py-3.5 px-6 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20 text-sm text-on-surface">
                                @forelse($jadwals ?? [] as $jadwal)
                                <tr class="hover:bg-surface-container-low/40 transition-colors">
                                    <td class="py-4 px-6 font-semibold text-on-surface">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-xs">
                                                {{ strtoupper(substr($jadwal->penanggung_jawab ?? 'DR', 0, 2)) }}
                                            </div>
                                            <span>{{ $jadwal->penanggung_jawab ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-secondary text-xs">{{ $jadwal->waktu ?? '-' }}</td>
                                    <td class="py-4 px-4 font-medium">{{ $jadwal->judul ?? '-' }}</td>
                                    <td class="py-4 px-4 text-secondary text-xs">{{ $jadwal->tanggal ?? '-' }}</td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                            @if(($jadwal->kategori ?? 'driver') == 'driver') bg-primary-container/20 text-primary
                                            @else bg-amber-100 text-amber-800 @endif">
                                            {{ ucfirst($jadwal->kategori ?? 'driver') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-secondary text-xs">{{ $jadwal->keterangan ?? '-' }}</td>
                                    <td class="py-4 px-6 text-right">
                                        <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" class="inline-flex gap-1 items-center">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="openEditJadwalModal({{ json_encode([
                                                'id' => $jadwal->id,
                                                'kategori' => $jadwal->kategori ?? 'driver',
                                                'penanggung_jawab' => $jadwal->penanggung_jawab ?? '',
                                                'waktu' => $jadwal->waktu ?? '',
                                                'judul' => $jadwal->judul ?? '',
                                                'tanggal' => $jadwal->tanggal ?? date('Y-m-d'),
                                                'keterangan' => $jadwal->keterangan ?? '',
                                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }})" class="px-2.5 py-1 rounded-lg text-primary hover:bg-primary-fixed font-semibold text-xs transition-colors cursor-pointer">Edit</button>
                                            <button type="submit" onclick="return confirm('Hapus jadwal ini?')" class="px-2.5 py-1 rounded-lg text-error hover:bg-error-container/30 font-semibold text-xs transition-colors cursor-pointer">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-secondary text-sm">Belum ada data jadwal penugasan atau servis tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH JADWAL -->
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/40 backdrop-blur-xs hidden p-4" id="modal-tambah-jadwal">
        <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-xl p-6 flex flex-col gap-4 border border-outline-variant/40">
            <div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-2xl">event_note</span>
                    <h2 class="font-bold text-on-surface text-lg">Tambah Penugasan Driver / Servis</h2>
                </div>
                <button class="p-1.5 rounded-lg text-secondary hover:bg-surface-container transition-colors cursor-pointer" onclick="toggleModal(false)" type="button">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

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
                        <input type="text" name="penanggung_jawab" required placeholder="Nama Driver..." class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Jam / Waktu</label>
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
                    <input type="text" name="keterangan" placeholder="Contoh: Hauling Pit West ke Jetty" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-outline-variant/30">
                    <button class="px-4 py-2 rounded-xl text-secondary hover:bg-surface-container font-semibold text-xs transition-colors cursor-pointer" onclick="toggleModal(false)" type="button">Batal</button>
                    <button class="px-5 py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-semibold text-xs transition-all shadow-sm cursor-pointer" type="submit">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT JADWAL -->
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/40 backdrop-blur-xs hidden p-4" id="modal-edit-jadwal" onclick="if(event.target === this) toggleEditModal(false)">
        <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-xl p-6 flex flex-col gap-4 border border-outline-variant/40">
            <div class="flex items-center justify-between pb-3 border-b border-outline-variant/30">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-2xl">edit_calendar</span>
                    <h2 class="font-bold text-on-surface text-lg" id="modalEditTitle">Edit Penugasan Driver / Servis</h2>
                </div>
                <button class="p-1.5 rounded-lg text-secondary hover:bg-surface-container transition-colors cursor-pointer" onclick="toggleEditModal(false)" type="button">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <form id="form-edit-jadwal" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-secondary mb-1">Kategori / Jenis Kegiatan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl bg-surface-container-low cursor-pointer">
                            <input id="edit_kategori_driver" class="text-primary" name="kategori" type="radio" value="driver"/>
                            <span class="text-xs font-semibold text-on-surface">Penugasan Driver</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl bg-surface-container-low cursor-pointer">
                            <input id="edit_kategori_servis" class="text-primary" name="kategori" type="radio" value="servis"/>
                            <span class="text-xs font-semibold text-on-surface">Servis Armada</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Penanggung Jawab / Driver</label>
                        <input type="text" id="edit_penanggung_jawab" name="penanggung_jawab" required placeholder="Nama Driver..." class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Jam / Waktu</label>
                        <input type="text" id="edit_waktu" name="waktu" required placeholder="07:00 - 15:00 WITA" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Judul / Unit Kendaraan</label>
                        <input type="text" id="edit_judul" name="judul" required placeholder="DT-01 Mitsubishi Fuso" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary mb-1">Tanggal</label>
                        <input type="date" id="edit_tanggal" name="tanggal" required class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-secondary mb-1">Keterangan / Rute Tugas</label>
                    <input type="text" id="edit_keterangan" name="keterangan" placeholder="Contoh: Hauling Pit West ke Jetty" class="w-full p-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-1 focus:ring-primary border border-outline-variant/30"/>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-outline-variant/30">
                    <button class="px-4 py-2 rounded-xl text-secondary hover:bg-surface-container font-semibold text-xs transition-colors cursor-pointer" onclick="toggleEditModal(false)" type="button">Batal</button>
                    <button class="px-5 py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-semibold text-xs transition-all shadow-sm cursor-pointer" type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
        function toggleModal(show) {
            document.getElementById('modal-tambah-jadwal').classList.toggle('hidden', !show);
        }

        function toggleEditModal(show) {
            const modal = document.getElementById('modal-edit-jadwal');
            if (modal) modal.classList.toggle('hidden', !show);
        }

        function openEditJadwalModal(data) {
            if (!data) return;

            const form = document.getElementById('form-edit-jadwal');
            form.action = "{{ url('/jadwal') }}/" + data.id;

            if (data.kategori === 'servis') {
                document.getElementById('edit_kategori_servis').checked = true;
            } else {
                document.getElementById('edit_kategori_driver').checked = true;
            }

            document.getElementById('edit_penanggung_jawab').value = data.penanggung_jawab || '';
            document.getElementById('edit_waktu').value = data.waktu || '';
            document.getElementById('edit_judul').value = data.judul || '';
            document.getElementById('edit_tanggal').value = data.tanggal || '';
            document.getElementById('edit_keterangan').value = data.keterangan || '';

            toggleEditModal(true);
        }

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

        document.addEventListener('keydown', (e) => { 
            if (e.key === 'Escape') {
                toggleModal(false);
                toggleEditModal(false);
            }
        });
    </script>
</body>
</html>