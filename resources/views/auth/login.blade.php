<!DOCTYPE html>
<html class="h-full" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login</title>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config={darkMode:"class",theme:{extend:{"colors":{"surface-container-lowest":"#ffffff","tertiary":"#005a89","surface-variant":"#d3e4fe","on-secondary-container":"#5c647a","inverse-primary":"#b5c4ff","surface-tint":"#0051e1","on-tertiary-fixed":"#001d31","tertiary-fixed-dim":"#93ccff","on-primary-fixed-variant":"#003cac","on-error-container":"#93000a","on-secondary-fixed-variant":"#3f465c","on-tertiary-container":"#e7f2ff","surface-container-high":"#dce9ff","outline-variant":"#c3c5d8","error":"#ba1a1a","surface-dim":"#cbdbf5","on-secondary-fixed":"#131b2e","secondary-fixed-dim":"#bec6e0","surface":"#f8f9ff","inverse-surface":"#213145","on-primary-container":"#eeefff","surface-container-highest":"#d3e4fe","primary-container":"#1e60f6","on-surface":"#0b1c30","on-tertiary":"#ffffff","surface-bright":"#f8f9ff","primary-fixed":"#dce1ff","on-background":"#0b1c30","primary":"#0048cc","secondary":"#565e74","on-primary":"#ffffff","on-primary-fixed":"#00164d","secondary-fixed":"#dae2fd","tertiary-fixed":"#cce5ff","on-surface-variant":"#434655","inverse-on-surface":"#eaf1ff","tertiary-container":"#0073ae","background":"#f8f9ff","on-tertiary-fixed-variant":"#004b73","surface-container-low":"#eff4ff","primary-fixed-dim":"#b5c4ff","outline":"#737687","error-container":"#ffdad6","on-error":"#ffffff","on-secondary":"#ffffff","secondary-container":"#dae2fd","surface-container":"#e5eeff"},"borderRadius":{"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},"fontFamily":{"headline-xl":["Plus Jakarta Sans"],"body-sm":["Inter"],"label-sm":["Inter"],"label-lg":["Inter"],"body-md":["Inter"],"headline-sm":["Plus Jakarta Sans"],"headline-lg":["Plus Jakarta Sans"]}}}};</script>
</head>
<body class="min-h-full bg-background font-body-md text-on-surface antialiased flex flex-col justify-between selection:bg-primary-container selection:text-on-primary-container relative overflow-x-hidden">
    
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(30,96,246,0.06),rgba(248,249,255,0))]"></div>
    
    <!-- Header Logo & Nama -->
    <header class="relative z-10 w-full pt-6 px-6 md:px-10 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="DriveControl Logo" class="w-full h-full object-contain">
            </div>
            <div class="flex flex-col">
                <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight leading-none font-bold">DriveControl</span>
            </div>
        </div>
    </header>

    <!-- Main Content / Form Login -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center w-full px-6 md:px-10 py-10">
        <div class="flex flex-col w-full items-center justify-center relative">
            <div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[720px] h-[520px] bg-gradient-to-b from-primary/10 via-surface-container-high/40 to-transparent blur-3xl opacity-70"></div>
            
            <div class="relative w-full max-w-[480px] mx-auto flex flex-col items-center">
                <div class="w-full bg-surface-container-lowest rounded-2xl shadow-xl shadow-on-surface/5 p-6 sm:p-9 relative border border-surface-container-high">
                    
                    <div class="flex flex-col mb-6">
                        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">
                            Masuk ke Sistem
                        </h1>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                            Kelola pemesanan, perizinan, dan armada operasional.
                        </p>
                    </div>

                    <!-- Notifikasi Error / Success dari Laravel -->
                    @if($errors->any())
                        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-xl text-sm mb-4 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">error</span>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-xl text-sm mb-4 font-medium">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Form Login yang terhubung ke AuthController -->
                    <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="email">
                                Email Perusahaan
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-on-surface-variant pointer-events-none text-[20px]">
                                    mail
                                </span>
                                <input class="w-full h-11 pl-11 pr-4 bg-surface rounded-xl font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary-container transition-all" id="email" name="email" placeholder="contoh: pegawai@sekawan.com" type="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="font-label-md text-label-md text-on-surface" for="password">
                                    Kata Sandi
                                </label>
                                <a class="font-label-md text-label-md text-primary font-medium hover:underline" href="#">
                                    Lupa Kata Sandi?
                                </a>
                            </div>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-on-surface-variant pointer-events-none text-[20px]">
                                    lock
                                </span>
                                <input class="w-full h-11 pl-11 pr-11 bg-surface rounded-xl font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary-container transition-all" id="password" name="password" placeholder="Masukkan kata sandi akun" type="password" required>
                                <button aria-label="Toggle password visibility" class="absolute right-3.5 text-on-surface-variant hover:text-on-surface focus:outline-none transition-colors" id="togglePassword" type="button">
                                    <span class="material-symbols-outlined text-[20px]" id="eyeIcon">visibility</span>
                                </button>
                            </div>
                        </div>

                        <button class="w-full h-12 mt-2 bg-primary hover:bg-primary-container active:scale-[0.99] text-on-primary rounded-xl font-label-lg text-label-lg tracking-wide shadow-md shadow-primary/25 flex items-center justify-center gap-2 transition-all cursor-pointer" type="submit">
                            <span>Masuk ke Dashboard</span>
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>
        
        <!-- Script Toggle Password Visibility -->
        <script>
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggleBtn && passwordInput && eyeIcon) {
                toggleBtn.addEventListener('click', () => {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }
        </script>
    </main>

</body>
</html>