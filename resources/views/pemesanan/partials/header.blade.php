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
