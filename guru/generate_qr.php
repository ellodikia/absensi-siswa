<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$secret_key = "ABSEN_RAHASIA_SMK_2024";
$timestamp = floor(time() / 30);
$token = md5($secret_key . $timestamp);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <meta http-equiv="refresh" content="30">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-4 md:p-8 space-y-6 flex flex-col items-center justify-center flex-1">
            <div class="text-center mb-6">
                <h1 class="text-2xl md:text-3xl font-black text-slate-800 uppercase tracking-tight">Scan Absensi</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">
                    QR diperbarui dalam <span id="timer" class="text-rose-500 font-mono">30</span> detik
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative group transition-all hover:shadow-md">
                <div class="bg-white p-4 rounded-2xl flex items-center justify-center w-64 h-64 md:w-80 md:h-80" id="qrcode-container">
                    </div>
            </div>

            <div class="flex items-center gap-3 bg-indigo-50 px-6 py-3 rounded-2xl border border-indigo-100 animate-pulse">
                <i class="fas fa-sync-alt fa-spin text-indigo-600 text-xs"></i>
                <span class="font-black text-indigo-600 tracking-widest uppercase text-[10px]">Menunggu Scan Siswa...</span>
            </div>
        </main>

        <script>
            const qrTokenData = "<?= $token ?>";
            
            new QRCode(document.getElementById("qrcode-container"), {
                text: qrTokenData,
                width: 280,
                height: 280,
                colorDark : "#1e1b4b", 
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });

            let timeLeft = 30;
            const timerSpan = document.getElementById('timer');
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