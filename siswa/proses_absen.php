<?php
include '../include/config.php';
header('Content-Type: application/json'); // Set output sebagai JSON

if ($_SESSION['role'] != 'siswa') { 
    echo json_encode(['status' => 'error', 'message' => 'Akses Ditolak']);
    exit; 
}

// Pastikan request yang masuk adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan']);
    exit;
}

// Ambil data JSON yang dikirim via AJAX
$data = json_decode(file_get_contents('php://input'), true);

$token_client = $data['token'] ?? '';
$user_lat = $data['lat'] ?? '';
$user_lng = $data['lng'] ?? '';

if (empty($user_lat) || empty($user_lng)) {
    echo json_encode(['status' => 'error', 'message' => 'GPS Tidak Terdeteksi! Pastikan izin lokasi aktif.']);
    exit;
}

$sekolah_lat = -6.200000; 
$sekolah_lng = 106.816666; 
$batas_radius = 100; 

function hitungJarak($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    return acos($dist) * 60 * 1.1515 * 1609.344; 
}

$jarak_siswa = round(hitungJarak($sekolah_lat, $sekolah_lng, $user_lat, $user_lng));

if ($jarak_siswa > $batas_radius) {
    echo json_encode(['status' => 'error', 'message' => "Gagal! Anda berada di luar radius sekolah ($jarak_siswa Meter)."]);
    exit;
}

$secret_key = "ABSEN_RAHASIA_SMK_2024";
$t_now = floor(time() / 30);
$valid_tokens = [md5($secret_key . $t_now), md5($secret_key . ($t_now - 1))];

if (in_array($token_client, $valid_tokens)) {
    $user_id = $_SESSION['user_id'];
    $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$user_id'"));
    $siswa_id = $siswa['id'];
    
    $tgl = date('Y-m-d');
    $jam = date('H:i:s');
    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id = '$siswa_id' AND tanggal = '$tgl'");
    
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, jam_masuk, status) VALUES ('$siswa_id', '$tgl', '$jam', 'hadir')");
        echo json_encode(['status' => 'success', 'message' => "Berhasil Absen! Jarak Anda: $jarak_siswa Meter."]);
    } else {
        echo json_encode(['status' => 'warning', 'message' => 'Anda sudah melakukan absensi hari ini!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'QR Code Kadaluarsa! Silakan scan ulang QR yang terbaru di layar.']);
}
?>