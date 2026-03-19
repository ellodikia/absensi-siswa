<?php
include '../include/config.php';
if ($_SESSION['role'] != 'siswa') { die("Akses Ditolak"); }

$sekolah_lat = -6.200000; // SESUAIKAN KOORDINAT SEKOLAH ANDA
$sekolah_lng = 106.816666; 
$batas_radius = 100; // JARAK MAKSIMAL (METER)

function hitungJarak($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    return acos($dist) * 60 * 1.1515 * 1609.344;
}

$user_id = $_SESSION['user_id'];
$siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$user_id'"));
$siswa_id = $siswa['id'];

$token_client = $_GET['token'] ?? '';
$user_lat = $_GET['lat'] ?? '';
$user_lng = $_GET['lng'] ?? '';

if (empty($user_lat) || empty($user_lng)) {
    echo "<script>alert('GPS Tidak Terdeteksi!'); window.location='scan.php';</script>";
    exit;
}

$jarak_siswa = round(hitungJarak($sekolah_lat, $sekolah_lng, $user_lat, $user_lng));

if ($jarak_siswa > $batas_radius) {
    echo "<script>alert('Gagal! Anda berada di luar radius sekolah ($jarak_siswa Meter).'); window.location='scan.php';</script>";
    exit;
}

$secret_key = "ABSEN_RAHASIA_SMK_2024";
$t_now = floor(time() / 30);
$valid_tokens = [md5($secret_key . $t_now), md5($secret_key . ($t_now - 1))];

if (in_array($token_client, $valid_tokens)) {
    $tgl = date('Y-m-d');
    $jam = date('H:i:s');
    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id = '$siswa_id' AND tanggal = '$tgl'");
    
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, jam_masuk, status) VALUES ('$siswa_id', '$tgl', '$jam', 'hadir')");
        echo "<script>alert('Berhasil Absen! Jarak: $jarak_siswa m'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Sudah Absen Hari Ini!'); window.location='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('QR Code Kadaluarsa!'); window.location='scan.php';</script>";
}
?>