<?php
include '../include/config.php';

if ($_SESSION['role'] != 'siswa') { die("Akses Ditolak"); }

// 1. Ambil data siswa yang sedang login
$user_id = $_SESSION['user_id'];
$query_siswa = mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$user_id'");
$data_siswa = mysqli_fetch_assoc($query_siswa);
$siswa_id = $data_siswa['id'];

// 2. Ambil token dari URL (Hasil Scan)
$token_client = isset($_GET['token']) ? $_GET['token'] : '';
$secret_key = "ABSEN_RAHASIA_SMK_2024"; // HARUS SAMA dengan yang di guru/generate_qr.php

// 3. Validasi Token (Anti-Titip)
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
            echo "<script>alert('Absen Berhasil!'); window.location='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Anda sudah melakukan absen hari ini!'); window.location='dashboard.php';</script>";
    }
} else {
    // Token tidak cocok (Artinya QR sudah ganti/kadaluarsa atau hasil foto teman)
    echo "<script>alert('Gagal! QR Code kadaluarsa. Silahkan scan QR yang terbaru di layar guru.'); window.location='scan.php';</script>";
}
?>