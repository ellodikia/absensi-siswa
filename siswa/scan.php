<?php
include '../include/config.php';
if ($_SESSION['role'] != 'siswa') { header("Location: ../login.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-4 md:p-8 flex flex-col items-center">
            <div class="w-full max-w-lg bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-100 text-center">
                <h2 class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight">Kamera Scan</h2>
                <p class="text-slate-400 font-medium mb-8 text-sm leading-relaxed px-4">Arahkan kamera ke QR Code yang ada di layar proyektor depan kelas</p>

                <div id="gps-status" class="mb-8 p-5 bg-amber-50 rounded-2xl text-xs font-black uppercase tracking-widest text-amber-600 border border-amber-100 flex items-center justify-center gap-3">
                    <i class="fas fa-location-arrow animate-bounce"></i> Mendeteksi GPS...
                </div>

                <div class="relative">
                    <div id="reader" class="overflow-hidden rounded-[2rem] border-8 border-slate-50 shadow-inner relative z-10 aspect-square"></div>
                    <div class="absolute inset-0 border-[20px] border-white/20 rounded-[2rem] z-20 pointer-events-none"></div>
                </div>

                <div id="result" class="hidden mt-8 p-5 bg-emerald-50 text-emerald-600 rounded-2xl font-black text-xs uppercase tracking-[0.2em] animate-pulse border border-emerald-100">
                    Memproses Kehadiran...
                </div>
            </div>
        </main>

        <script>
            let userLat = null, userLng = null;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLat = position.coords.latitude;
                        userLng = position.coords.longitude;
                        const status = document.getElementById('gps-status');
                        status.innerHTML = '<i class="fas fa-check-circle"></i> Lokasi Siap. Silakan Scan.';
                        status.className = 'mb-8 p-5 bg-emerald-50 rounded-2xl text-xs font-black uppercase tracking-widest text-emerald-600 border border-emerald-100 flex items-center justify-center gap-3';
                    },
                    function() {
                        alert("Gagal mendapatkan lokasi. Pastikan GPS aktif!");
                    },
                    { enableHighAccuracy: true }
                );
            }

            function onScanSuccess(decodedText) {
                if (!userLat || !userLng) {
                    alert("Tunggu, GPS belum terdeteksi!");
                    return;
                }
                document.getElementById('result').classList.remove('hidden');
                html5QrcodeScanner.clear();
                window.location.href = decodedText + "&lat=" + userLat + "&lng=" + userLng;
            }

            let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} });
            html5QrcodeScanner.render(onScanSuccess);
        </script>
    </div>
</body>
</html>