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
                <p class="text-gray-500 mb-6 text-sm">Scan QR Code yang ditampilkan oleh Guru di depan kelas</p>

                <div id="reader" class="overflow-hidden rounded-2xl border-4 border-indigo-100 mb-6"></div>

                <div id="result" class="hidden p-4 bg-yellow-100 text-yellow-800 rounded-lg animate-pulse">
                    Memproses data...
                </div>
            </div>
        </main>

        <script>
            function onScanSuccess(decodedText, decodedResult) {
                // decodedText berisi URL: https://.../siswa/proses_absen.php?token=xyz
                document.getElementById('result').classList.remove('hidden');
                
                // Matikan kamera setelah berhasil scan
                html5QrcodeScanner.clear();

                // Redirect ke link yang ada di QR Code
                window.location.href = decodedText;
            }

            function onScanFailure(error) {
                // Biarkan saja, dia akan terus mencoba scan
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 10, qrbox: {width: 250, height: 250} }
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        </script>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>