<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

include '../include/config.php';
header('Content-Type: application/json'); 

try {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') { 
        echo json_encode(['status' => 'error', 'message' => 'Akses Ditolak. Sesi Anda mungkin sudah habis, silakan login ulang.']);
        exit; 
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan']);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Data QR Code tidak terbaca oleh sistem.']);
        exit;
    }

    $token_client = $data['token'] ?? '';
    $user_lat = $data['lat'] ?? '';
    $user_lng = $data['lng'] ?? '';

    if (empty($user_lat) || empty($user_lng)) {
        echo json_encode(['status' => 'error', 'message' => 'GPS Tidak Terdeteksi! Pastikan izin lokasi aktif.']);
        exit;
    }

    $sekolah_lat = 3.53863109651153; 
    $sekolah_lng = 98.66941971653848;
    $batas_radius = 100; 

    function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        if ($lat1 == $lat2 && $lon1 == $lon2) return 0;
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos(min(max($dist, -1.0), 1.0));
        return $dist * 60 * 1.1515 * 1609.344; 
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
        if (!isset($_SESSION['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi user_id tidak ditemukan. Silakan login ulang.']);
            exit;
        }
        $user_id = $_SESSION['id'];
        
        $query_siswa = mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$user_id'");
        if (!$query_siswa) {
            throw new Exception("Database Error (Siswa): " . mysqli_error($conn));
        }
        
        $siswa = mysqli_fetch_assoc($query_siswa);
        if (!$siswa) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal: Data diri siswa Anda tidak ditemukan di database.']);
            exit;
        }
        
        $siswa_id = $siswa['id'];
        $tgl = date('Y-m-d');
        $jam = date('H:i:s');
        
        $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id = '$siswa_id' AND tanggal = '$tgl'");
        if (!$cek) {
            throw new Exception("Database Error (Cek Absensi): " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($cek) == 0) {
            $insert = mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, jam_masuk, status) VALUES ('$siswa_id', '$tgl', '$jam', 'hadir')");
            if (!$insert) {
                throw new Exception("Database Error (Insert Absensi): " . mysqli_error($conn));
            }
            echo json_encode(['status' => 'success', 'message' => "Berhasil Absen! Jarak Anda: $jarak_siswa Meter."]);
        } else {
            echo json_encode(['status' => 'warning', 'message' => 'Anda sudah melakukan absensi hari ini!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'QR Code Kadaluarsa! Silakan scan ulang QR yang terbaru di layar.']);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'System Error: ' . $e->getMessage()
    ]);
}
?>