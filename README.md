# DriveControl - Vehicle Management System (VMS)
> *Sistem Monitoring Armada Tambang & Pemesanan Kendaraan Operasional*

Aplikasi web untuk memantau manajemen armada, konsumsi bahan bakar (BBM), jadwal servis, serta alur pemesanan kendaraan operasional tambang dengan sistem persetujuan berjenjang.

---

## 📋 Spesifikasi Sistem & Lingkungan (Environment)
* **Framework:** Laravel 13.36.0
* **PHP Version:** PHP 8.5.8
* **Database:** MySQL (`kendaraan_sekawan`)
* **Frontend:** Tailwind CSS, Blade Templates, Google Fonts & Material Symbols

---

## 👥 Daftar Kredensial Pengguna (Login & Simulasi Role)
Aplikasi ini dilengkapi dengan fitur *Dev Switch Role* untuk memudahkan pengujian cepat antar-peran (atau dapat diakses melalui halaman `/login`):

| Nama Pengguna | Peran (Role) | Akses Level / Keterangan | Route / Shortcut Simulasi |
| :--- | :--- | :--- | :--- |
| **Admin Tambang** | `admin` | Input pemesanan, assign driver & approver, manajemen armada | `/dev-switch/{id}` |
| **Atasan / Pit Lead** | `atasan` | Approver Level 1 (Persetujuan operasional site) | `/dev-switch/{id}` |
| **Manajer Site** | `manajer` | Approver Level 2 (Persetujuan final / manajerial) | `/dev-switch/{id}` |
| **Staff Operasional** | `staff` | Pengajuan pemesanan & monitoring dashboard | `/dev-switch/{id}` |

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah berikut di terminal untuk menjalankan proyek di komputer lokal Anda:

1. **Install dependensi PHP melalui Composer:**
   ```bash
   composer install