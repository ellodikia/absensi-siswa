<?php
include '../include/config.php';

if ($_SESSION['role'] != 'siswa') { die("Akses Ditolak"); }

// ==========================================
// KONFIGURASI LOKASI SEKOLAH (UBAH INI)
// ==========================================
// Buka Google Maps, cari sekolah Anda, klik kanan di titik gedungnya lalu copy koordinatnya.
$sekolah_lat = 3.538620; // GANTI DENGAN LATITUDE SEKOLAH ANDA (Contoh: -6.200000)
$sekolah_lng = 98.669411; // GANTI DENGAN LONGITUDE SEKOLAH ANDA (Contoh: 106.816666)
$batas_radius = 1000; // Batas maksimal jarak dalam METER (Saran: 50 atau 100)

// Fungsi Menghitung Jarak (Rumus Haversine) Output dalam METER
function hitungJarak($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $meters = $miles * 1609.344;
    return $meters;
}

// 1. Ambil data siswa yang sedang login
$user_id = $_SESSION['user_id'];
$query_siswa = mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$user_id'");
$data_siswa = mysqli_fetch_assoc($query_siswa);
$siswa_id = $data_siswa['id'];

// 2. Ambil token & lokasi GPS dari URL
$token_client = isset($_GET['token']) ? $_GET['token'] : '';
$user_lat = isset($_GET['lat']) ? $_GET['lat'] : '';
$user_lng = isset($_GET['lng']) ? $_GET['lng'] : '';

// 3. Validasi GPS (Apakah Koordinat Terkirim?)
if (empty($user_lat) || empty($user_lng)) {
    echo "<script>alert('Gagal! Lokasi GPS tidak ditemukan. Pastikan GPS menyala dan izinkan browser mengakses lokasi.'); window.location='scan.php';</script>";
    exit;
}

// 4. Hitung Jarak dan Validasi Radius
$jarak_siswa = hitungJarak($sekolah_lat, $sekolah_lng, $user_lat, $user_lng);
$jarak_bulat = round($jarak_siswa); // Bulatkan angkanya biar rapi

if ($jarak_siswa > $batas_radius) {
    // Jika lebih dari 50 meter, tolak!
    echo "<script>alert('ABSEN DITOLAK! Anda berada di luar area sekolah. (Jarak Anda: $jarak_bulat Meter). Maksimal radius adalah $batas_radius Meter.'); window.location='scan.php';</script>";
    exit;
}

// 5. Validasi Token (Anti-Titip 30 Detik)
$secret_key = "ABSEN_RAHASIA_SMK_2024"; // HARUS SAMA dengan yang di guru/generate_qr.php
$timestamp_now = floor(time() / 30);
$timestamp_prev = $timestamp_now - 1; // Toleransi 30 detik sebelumnya (jika internet lambat)

$valid_token_now = md5($secret_key . $timestamp_now);
$valid_token_prev = md5($secret_key . $timestamp_prev);

if ($token_client === $valid_token_now || $token_client === $valid_token_prev) {
    $tgl = date('Y-m-d');
    $jam = date('H:i:s');

    // Cek apakah sudah absen hari ini agar tidak double
    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id = '$siswa_id' AND tanggal = '$tgl'");
    
    if (mysqli_num_rows($cek) == 0) {
        $insert = mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, jam_masuk, status) 
                                       VALUES ('$siswa_id', '$tgl', '$jam', 'hadir')");
        if ($insert) {
            echo "<script>alert('Absen Berhasil! Anda berada di dalam area sekolah ($jarak_bulat Meter).'); window.location='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Anda sudah melakukan absen hari ini!'); window.location='dashboard.php';</script>";
    }
} else {
    // Token tidak cocok
    echo "<script>alert('Gagal! QR Code sudah kadaluarsa. Silahkan scan QR yang terbaru di layar guru.'); window.location='scan.php';</script>";
}
?>