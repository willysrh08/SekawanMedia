<!-- FORM PENGAJUAN PEMESANAN KENDARAAN -->
<form action="{{ url('/pemesanan/ajukan') }}" method="POST" class="flex flex-col gap-6" id="booking-form">
    @csrf
    <input type="hidden" name="pegawai_id" value="{{ session('user_id') }}">

    <!-- LOKASI & TUJUAN -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
        <div class="flex items-center gap-2 border-b border-surface-container pb-3">
            <span class="material-symbols-outlined text-primary text-xl">near_me</span>
            <h2 class="font-headline font-semibold text-base text-on-surface">Lokasi Operasional & Tujuan</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface" for="lokasi_asal">Lokasi Asal / Titik Berangkat</label>
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
                <label class="text-xs font-semibold text-on-surface" for="tujuan">Tujuan Dinas / Area Tambang</label>
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

    <!-- WAKTU PEMAKAIAN -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
        <div class="flex items-center gap-2 border-b border-surface-container pb-3">
            <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
            <h2 class="font-headline font-semibold text-base text-on-surface">Jadwal Penggunaan Armada</h2>
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

    <!-- PILIH KENDARAAN & DRIVER (PREFERENSI) -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-outline-variant/40 flex flex-col gap-5">
        <div class="flex items-center gap-2 border-b border-surface-container pb-3">
            <span class="material-symbols-outlined text-primary text-xl">directions_car</span>
            <h2 class="font-headline font-semibold text-base text-on-surface">Preferensi Armada & Pengemudi</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface" for="kendaraan_id">Pilihan Unit Kendaraan (Bisa ditetapkan Admin)</label>
                <div class="relative">
                    <select class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary appearance-none cursor-pointer" id="kendaraan_id" name="kendaraan_id">
                        <option value="">-- Tetapkan oleh Admin Pool nanti --</option>
                        @foreach($kendaraanList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->plat_nomor }}) - {{ ucfirst($k->status) }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-secondary text-lg">expand_more</span>
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface" for="driver_id">Driver Bertugas (Bisa ditetapkan Admin)</label>
                <div class="relative">
                    <select class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary appearance-none cursor-pointer" id="driver_id" name="driver_id">
                        <option value="">-- Tetapkan oleh Admin Pool nanti --</option>
                        @foreach($driverList as $d)
                            <option value="{{ $d->id }}">{{ $d->nama }} ({{ $d->telepon }})</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-3 pointer-events-none text-secondary text-lg">expand_more</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-on-surface" for="keperluan">Keperluan Tugas Dinas / Rincian Pekerjaan</label>
            <textarea class="w-full p-3 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant focus:outline-none focus:ring-1 focus:ring-primary" id="keperluan" name="keperluan" rows="3" required placeholder="Contoh: Inspeksi Pit West, evaluasi hauling jalan tambang, atau survey rutin logistik..."></textarea>
        </div>
    </div>

    <!-- TOMBOL SUBMIT -->
    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ url('/dashboard') }}" class="px-6 h-11 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-highest text-xs font-semibold flex items-center justify-center transition-colors">Batal</a>
        <button class="flex items-center gap-2 px-8 h-11 rounded-lg bg-primary text-on-primary hover:bg-primary-container text-xs font-semibold shadow-xs transition-all cursor-pointer" type="submit">
            <span>Kirim Pengajuan Pemesanan</span>
            <span class="material-symbols-outlined text-sm">send</span>
        </button>
    </div>
</form>
