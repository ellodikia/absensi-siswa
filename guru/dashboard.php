<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$tgl_sekarang = date('Y-m-d');

$hadir = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE tanggal='$tgl_sekarang' AND status='hadir'"));
$sakit = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE tanggal='$tgl_sekarang' AND status='sakit'"));
$izin  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE tanggal='$tgl_sekarang' AND status='izin'"));
$total_siswa = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM siswa"));
$belum_absen = $total_siswa - ($hadir + $sakit + $izin);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-4 md:p-8 space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="bg-emerald-100 w-12 h-12 rounded-2xl flex items-center justify-center text-emerald-600 mb-4">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Hadir</p>
                    <h3 class="text-2xl font-black text-slate-800"><?= $hadir ?></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="bg-amber-100 w-12 h-12 rounded-2xl flex items-center justify-center text-amber-600 mb-4">
                        <i class="fas fa-envelope-open-text text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sakit</p>
                    <h3 class="text-2xl font-black text-slate-800"><?= $sakit ?></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="bg-indigo-100 w-12 h-12 rounded-2xl flex items-center justify-center text-indigo-600 mb-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Izin</p>
                    <h3 class="text-2xl font-black text-slate-800"><?= $izin ?></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="bg-rose-100 w-12 h-12 rounded-2xl flex items-center justify-center text-rose-600 mb-4">
                        <i class="fas fa-user-times text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Alpa</p>
                    <h3 class="text-2xl font-black text-slate-800"><?= $belum_absen ?></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <h4 class="font-bold text-slate-800 mb-6 uppercase text-sm tracking-wider">Statistik Kehadiran</h4>
                    <div class="relative h-64">
                        <canvas id="absensiChart"></canvas>
                    </div>
                </div>
                <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl text-white flex flex-col justify-center relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-2xl font-black mb-4 uppercase tracking-tight">Mulai Absensi</h2>
                        <p class="text-indigo-100 mb-8 font-medium leading-relaxed">Tampilkan QR Code. Pastikan seluruh siswa dapat menjangkau kode tersebut.</p>
                        <a href="generate_qr.php" class="inline-block bg-white text-indigo-600 font-black py-4 px-8 rounded-2xl hover:bg-indigo-50 transition-all shadow-lg active:scale-95">
                            BUKA QR CODE <i class="fas fa-qrcode ml-2"></i>
                        </a>
                    </div>
                    <i class="fas fa-rocket absolute -right-12 -bottom-12 text-[12rem] text-indigo-500 opacity-20 rotate-12"></i>
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
                        data: [<?= $hadir ?>, <?= $sakit ?>, <?= $izin ?>, <?= $belum_absen ?>],
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#f43f5e'],
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { weight: 'bold' } } } },
                    cutout: '70%'
                }
            });
        </script>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>