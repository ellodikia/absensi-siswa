<?php
date_default_timezone_set('Asia/Jakarta');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "absensi_siswa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();

$base_url = "http://localhost/absensi-qr/"; 

function escape($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars($data));
}
?>