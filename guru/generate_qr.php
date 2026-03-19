<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$secret_key = "ABSEN_RAHASIA_SMK_2024";
$timestamp = floor(time() / 30);
$token = md5($secret_key . $timestamp);

$qr_content = $base_url . "siswa/proses_absen.php?token=" . $token;
$qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($qr_content);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta http-equiv="refresh" content="30">
</head>
<body class="bg-indigo-900 flex overflow-hidden">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen bg-slate-50 md:rounded-l-[3rem] shadow-2xl">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-4 md:p-8 flex flex-col items-center justify-center flex-1">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tighter uppercase italic">Scan Absensi Sekarang</h1>
                <p class="text-slate-400 font-bold mt-2">QR Code diperbarui dalam <span id="timer" class="text-rose-500 font-mono">30</span> detik</p>
            </div>
            
            <div class="relative group">
                <div class="absolute -inset-4 bg-indigo-500/20 rounded-[3rem] blur-xl group-hover:bg-indigo-500/30 transition-all"></div>
                <div class="relative p-6 md:p-10 bg-white border-[12px] border-white rounded-[3rem] shadow-2xl transition-transform hover:scale-[1.02]">
                    <img src="<?= $qr_image_url ?>" alt="QR Code" class="w-64 h-64 md:w-80 md:h-80 rounded-2xl">
                </div>
            </div>

            <div class="mt-12 flex items-center gap-4 bg-indigo-50 px-8 py-4 rounded-2xl border border-indigo-100 animate-pulse">
                <i class="fas fa-sync-alt fa-spin text-indigo-600"></i>
                <span class="font-black text-indigo-600 tracking-widest uppercase text-xs">Menunggu Scan Siswa...</span>
            </div>
        </main>

        <script>
            let timeLeft = 30;
            const timerSpan = document.getElementById('timer');
            setInterval(() => {
                timeLeft--;
                timerSpan.innerText = timeLeft;
                if(timeLeft <= 0) location.reload();
            }, 1000);
        </script>
    </div>
</body>
</html>