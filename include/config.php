<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "absensi_siswa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();

// Helper untuk base URL agar link tidak patah
$base_url = "http://localhost/absensi-qr/"; 

// Fungsi anti-injection sederhana
function escape($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars($data));
}
?>