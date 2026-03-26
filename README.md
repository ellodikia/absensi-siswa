# R-ABSEN: Sistem Absensi Siswa Berbasis QR Code & Geolocation

**R-ABSEN** adalah aplikasi manajemen absensi siswa berbasis web yang dirancang untuk meningkatkan efisiensi dan akurasi pencatatan kehadiran. Sistem ini menggabungkan teknologi **Dynamic QR Code** dan **Geolocation (GPS)** untuk memastikan validitas kehadiran siswa tepat di lokasi sekolah.

---

## 🚀 Fitur Utama

### 👨‍🎓 Siswa
* **Scan QR Code:** Absensi masuk menggunakan QR Code dinamis.
* **Verifikasi Geolocation:** Validasi radius GPS untuk mencegah manipulasi jarak jauh.
* **Riwayat Kehadiran:** Cek rekap absensi pribadi secara *real-time*.
* **Manajemen Profil:** Kelola data diri siswa.

### 👨‍🏫 Guru
* **Dynamic QR Generator:** Membuat QR Code yang berubah setiap 30 detik.
* **Manajemen Data:** Mengelola data siswa di kelas terkait.
* **Rekap & Laporan:** Laporan harian/bulanan dengan fitur **Export Excel**.
* **Manual Override:** Input absensi manual untuk kondisi darurat.

### ⚡ Admin
* **User Management:** Kontrol penuh data guru, siswa, dan akun sistem.
* **Statistik Dashboard:** Visualisasi data kehadiran sekolah secara menyeluruh.

---

## 🛠️ Tech Stack

* **Language:** PHP 8.x (Native)
* **Database:** MySQL / MariaDB
* **CSS Framework:** Tailwind CSS
* **Library:**
    * [Html5-QRCode](https://github.com/mebjas/html5-qrcode) / Instascan (Scanner)
    * [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) (Export Excel)
    * [FontAwesome](https://fontawesome.com/) (Icons)

---

## 📦 Instalasi

### 1. Persiapan
Pastikan perangkat kamu sudah terinstal server lokal seperti **XAMPP** atau **Laragon**.

### 2. Clone atau Download Project
Letakkan folder project ke dalam direktori server:
* **XAMPP:** `C:/xampp/htdocs/absensi-siswa`
* **Laragon:** `C:/laragon/www/absensi-siswa`

### 3. Setup Database
1. Buka **phpMyAdmin**.
2. Buat database baru dengan nama `absensi_siswa`.
3. Import file `absensi_siswa.sql` yang tersedia di root folder project.

### 4. Konfigurasi Koneksi
Buka file `include/config.php` dan sesuaikan kredensial database:
```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "absensi_siswa";