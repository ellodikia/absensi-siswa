<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

// Rahasia untuk token (Ganti sesuka hati)
$secret_key = "ABSEN_RAHASIA_SMK_2024";

// Token berubah tiap 30 detik berdasarkan waktu server
$timestamp = floor(time() / 30);
$token = md5($secret_key . $timestamp);

// Data QR berisi link ke proses absen siswa
$qr_content = $base_url . "siswa/proses_absen.php?token=" . $token;
$qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($qr_content);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate QR Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta http-equiv="refresh" content="30">
</head>
<body class="bg-indigo-900 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6 flex flex-col items-center justify-center flex-1 bg-white mx-4 my-6 rounded-3xl shadow-xl text-center">
            <h1 class="text-3xl font-extrabold text-gray-800 mb-2 italic">SCAN ABSENSI SEKARANG</h1>
            <p class="text-gray-500 mb-8 font-medium">QR Code ini akan otomatis berubah dalam <span id="timer" class="text-red-600 font-bold">30</span> detik</p>
            
            <div class="p-6 bg-white border-8 border-indigo-100 rounded-3xl shadow-lg mb-8 transition-all hover:scale-105">
                <img src="<?= $qr_image_url ?>" alt="QR Code" class="w-64 h-64 md:w-80 md:h-80">
            </div>

            <div class="flex items-center space-x-3 text-indigo-600 animate-pulse">
                <i class="fas fa-sync-alt fa-spin"></i>
                <span class="font-bold tracking-widest uppercase text-sm">Menunggu Scan Siswa...</span>
            </div>
        </main>

        <script>
            let timeLeft = 30;
            let timerSpan = document.getElementById('timer');
            setInterval(() => {
                timeLeft--;
                timerSpan.innerText = timeLeft;
                if(timeLeft <= 0) location.reload();
            }, 1000);
        </script>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>