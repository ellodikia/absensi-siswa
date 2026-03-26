<?php
include '../include/config.php';
if ($_SESSION['role'] != 'siswa') { header("Location: ../login.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #reader__dashboard_section_csr button { 
            background-color: #4f46e5 !important;
            color: white !important;
            padding: 8px 16px !important;
            border-radius: 12px !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
            font-size: 10px !important;
            border: none !important;
            margin: 5px !important;
        }
        #reader__camera_selection {
            padding: 8px !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            font-weight: bold !important;
            font-size: 12px !important;
            outline: none !important;
            width: 100% !important;
            margin-bottom: 10px !important;
        }
    </style>
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-4 md:p-8 flex flex-col items-center">
            <div class="w-full max-w-lg bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-100 text-center">
                <h2 class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight">Camera Scan</h2>
                <p class="text-slate-400 font-medium mb-8 text-sm leading-relaxed px-4">Pilih kamera dan arahkan ke QR Code di layar.</p>

                <div id="gps-status" class="mb-8 p-5 bg-amber-50 rounded-2xl text-xs font-black uppercase tracking-widest text-amber-600 border border-amber-100 flex items-center justify-center gap-3">
                    <i class="fas fa-location-arrow animate-bounce"></i> Mendeteksi GPS...
                </div>

                <div class="relative bg-slate-50 rounded-[2rem] p-2">
                    <div id="reader" class="overflow-hidden rounded-[1.8rem]"></div>
                </div>

                <div id="result" class="hidden mt-8 p-5 bg-indigo-50 text-indigo-600 rounded-2xl font-black text-xs uppercase tracking-[0.2em] animate-pulse border border-indigo-100 flex items-center justify-center gap-3">
                    <i class="fas fa-circle-notch fa-spin text-lg"></i> Memproses Kehadiran...
                </div>                
            </div>
        <?php include '../include/footer.php';?>
        </main>

        <script>
            let userLat = null, userLng = null;
            let isProcessing = false; 

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLat = position.coords.latitude;
                        userLng = position.coords.longitude;
                        const status = document.getElementById('gps-status');
                        status.innerHTML = '<i class="fas fa-check-circle"></i> Lokasi Siap. Silakan Scan.';
                        status.className = 'mb-8 p-5 bg-emerald-50 rounded-2xl text-xs font-black uppercase tracking-widest text-emerald-600 border border-emerald-100 flex items-center justify-center gap-3';
                    },
                    function(error) {
                        let msg = "Gagal mendapatkan lokasi. Pastikan GPS aktif!";
                        if (error.code == 1) msg = "Akses lokasi ditolak! Izinkan browser mengakses lokasi.";
                        Swal.fire('Perhatian', msg, 'error');
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            }

            function onScanSuccess(decodedText) {
                if (isProcessing) return; 
                
                if (!userLat || !userLng) {
                    Swal.fire('Tunggu!', 'GPS belum siap. Mohon tunggu beberapa detik.', 'warning');
                    return;
                }

                isProcessing = true;
                html5QrcodeScanner.pause(true); 
                document.getElementById('result').classList.remove('hidden');

                fetch('proses_absen.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        token: decodedText, 
                        lat: userLat,
                        lng: userLng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('result').classList.add('hidden');
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#4f46e5'
                        }).then(() => { window.location.href = 'dashboard.php'; });
                    } else {
                        Swal.fire({
                            icon: data.status === 'warning' ? 'warning' : 'error',
                            title: data.status === 'warning' ? 'Sudah Absen' : 'Gagal',
                            text: data.message,
                            confirmButtonColor: '#4f46e5'
                        }).then(() => {
                            isProcessing = false;
                            html5QrcodeScanner.resume();
                        });
                    }
                })
                .catch(error => {
                    document.getElementById('result').classList.add('hidden');
                    Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error').then(() => {
                        isProcessing = false;
                        html5QrcodeScanner.resume();
                    });
                });
            }

            let html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
                fps: 10, 
                qrbox: {width: 250, height: 250},
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true
            });
            html5QrcodeScanner.render(onScanSuccess);
        </script>
    </div>
</body>
</html>