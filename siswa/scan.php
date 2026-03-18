<?php
include '../include/config.php';
if ($_SESSION['role'] != 'siswa') { header("Location: ../login.php"); exit; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan Absensi - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gray-100 flex">
    <?php include '../include/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6 flex flex-col items-center">
            <div class="w-full max-w-md bg-white p-6 rounded-3xl shadow-lg text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Arahkan Kamera</h2>
                <p class="text-gray-500 mb-4 text-sm">Scan QR Code yang ditampilkan oleh Guru di depan kelas</p>

                <div id="gps-status" class="mb-4 p-3 bg-yellow-50 rounded-lg text-sm font-semibold text-yellow-700 border border-yellow-200">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Mendeteksi lokasi GPS Anda...
                </div>

                <div id="reader" class="overflow-hidden rounded-2xl border-4 border-indigo-100 mb-6 relative z-10"></div>

                <div id="result" class="hidden p-4 bg-green-100 text-green-800 rounded-lg font-bold animate-pulse">
                    Memproses data kehadiran...
                </div>
            </div>
        </main>

        <script>
            let userLat = null;
            let userLng = null;

            // 1. Ambil Lokasi GPS Siswa
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLat = position.coords.latitude;
                        userLng = position.coords.longitude;
                        document.getElementById('gps-status').innerHTML = '<span class="text-green-700"><i class="fas fa-map-marker-alt mr-2"></i> Lokasi terdeteksi. Silakan scan QR.</span>';
                        document.getElementById('gps-status').className = 'mb-4 p-3 bg-green-50 rounded-lg text-sm font-semibold border border-green-200';
                    },
                    function(error) {
                        document.getElementById('gps-status').innerHTML = '<span class="text-red-700"><i class="fas fa-exclamation-triangle mr-2"></i> Gagal mendapatkan lokasi! Izinkan akses GPS di browser Anda.</span>';
                        document.getElementById('gps-status').className = 'mb-4 p-3 bg-red-50 rounded-lg text-sm font-semibold border border-red-200';
                    },
                    { enableHighAccuracy: true } // Memaksa akurasi tinggi
                );
            } else {
                document.getElementById('gps-status').innerHTML = '<span class="text-red-600">Browser tidak mendukung Geolocation.</span>';
            }

            // 2. Proses jika QR berhasil di-scan
            function onScanSuccess(decodedText, decodedResult) {
                // Cek apakah lokasi sudah didapatkan
                if (userLat === null || userLng === null) {
                    alert("Tunggu sebentar, lokasi Anda belum terdeteksi atau Anda belum mengizinkan akses lokasi (GPS)!");
                    return; // Hentikan proses jika belum ada GPS
                }

                document.getElementById('result').classList.remove('hidden');
                
                // Matikan kamera setelah berhasil scan
                html5QrcodeScanner.clear();

                // Tambahkan data latitude dan longitude ke URL proses absen
                // decodedText contoh: https://domain.com/siswa/proses_absen.php?token=xyz
                let finalUrl = decodedText + "&lat=" + userLat + "&lng=" + userLng;
                
                // Redirect ke pemrosesan
                window.location.href = finalUrl;
            }

            function onScanFailure(error) {
                // Biarkan saja jika belum pas, dia akan terus mencoba scan
            }

            // Inisialisasi Kamera
            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: {width: 250, height: 250} }
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        </script>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>