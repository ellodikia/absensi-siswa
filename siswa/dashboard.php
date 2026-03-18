<?php
include '../include/config.php';
if ($_SESSION['role'] != 'siswa') { header("Location: ../login.php"); exit; }

$user_id = $_SESSION['user_id'];
$siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE user_id='$user_id'"));
$siswa_id = $siswa['id'];
$tgl_ini = date('Y-m-d');

// Cek Status Absen Hari Ini
$cek_absen = mysqli_query($conn, "SELECT * FROM absensi WHERE siswa_id='$siswa_id' AND tanggal='$tgl_ini'");
$status_hari_ini = mysqli_fetch_assoc($cek_absen);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex">
    <?php include '../include/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6">
            <div class="bg-indigo-600 rounded-3xl p-8 text-white mb-6 shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold">Halo, <?= $siswa['nama_lengkap'] ?>!</h1>
                    <p class="text-indigo-100 mt-2 italic font-medium">"Disiplin adalah kunci kesuksesan."</p>
                </div>
                <i class="fas fa-graduation-cap absolute -right-10 -bottom-10 text-9xl text-indigo-500 opacity-50"></i>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border text-center">
                    <h4 class="text-gray-500 font-bold uppercase text-xs tracking-widest mb-4">Status Kehadiran Hari Ini</h4>
                    <?php if($status_hari_ini): ?>
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-6xl mb-3"></i>
                            <p class="text-xl font-bold">SUDAH ABSEN</p>
                            <p class="text-sm text-gray-500">Jam: <?= $status_hari_ini['jam_masuk'] ?></p>
                        </div>
                    <?php else: ?>
                        <div class="text-red-500">
                            <i class="fas fa-times-circle text-6xl mb-3"></i>
                            <p class="text-xl font-bold">BELUM ABSEN</p>
                            <a href="scan.php" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-full font-bold shadow-lg">Scan Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border">
                    <h4 class="text-gray-500 font-bold uppercase text-xs tracking-widest mb-4">Informasi Akun</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">NIS</span>
                            <span class="font-mono font-bold"><?= $siswa['nis'] ?></span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-500">Kelas</span>
                            <span class="font-bold"><?= $siswa['kelas_jurusan'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>