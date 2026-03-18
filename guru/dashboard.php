<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$tgl_sekarang = date('Y-m-d');

// Hitung Statistik Hari Ini
$hadir = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE tanggal='$tgl_sekarang' AND status='hadir'"));
$sakit = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE tanggal='$tgl_sekarang' AND status='sakit'"));
$izin  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE tanggal='$tgl_sekarang' AND status='izin'"));
$alpha = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE tanggal='$tgl_sekarang' AND status='alpha'"));
$total_siswa = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM siswa"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 flex">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Hadir</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?= $hadir ?></h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full text-green-600"><i class="fas fa-user-check fa-lg"></i></div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Sakit/Izin</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?= $sakit + $izin ?></h3>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full text-yellow-600"><i class="fas fa-envelope-open-text fa-lg"></i></div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-red-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Belum Absen</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?= $total_siswa - ($hadir + $sakit + $izin) ?></h3>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full text-red-600"><i class="fas fa-user-times fa-lg"></i></div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase">Total Siswa</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?= $total_siswa ?></h3>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full text-indigo-600"><i class="fas fa-users fa-lg"></i></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border">
                    <h4 class="font-bold text-gray-700 mb-4">Persentase Kehadiran Hari Ini</h4>
                    <canvas id="absensiChart" height="200"></canvas>
                </div>
                <div class="bg-indigo-900 p-8 rounded-2xl shadow-lg text-white flex flex-col justify-center">
                    <h2 class="text-2xl font-bold mb-2">Mulai Absensi?</h2>
                    <p class="text-indigo-200 mb-6">Pastikan proyektor atau layar laptop terlihat jelas oleh seluruh siswa di kelas.</p>
                    <a href="generate_qr.php" class="bg-yellow-500 text-indigo-900 font-bold py-3 px-6 rounded-xl text-center hover:bg-yellow-400 transition">
                        <i class="fas fa-qrcode mr-2"></i> Tampilkan QR Code Sekarang
                    </a>
                </div>
            </div>
        </main>

        <script>
            const ctx = document.getElementById('absensiChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Sakit', 'Izin', 'Belum Absen'],
                    datasets: [{
                        data: [<?= $hadir ?>, <?= $sakit ?>, <?= $izin ?>, <?= $total_siswa - ($hadir+$sakit+$izin) ?>],
                        backgroundColor: ['#22c55e', '#eab308', '#3b82f6', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        </script>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>