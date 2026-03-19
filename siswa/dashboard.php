<?php
include '../include/config.php';
if ($_SESSION['role'] != 'siswa') { header("Location: ../login.php"); exit; }

$user_id = $_SESSION['user_id'];
$siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE user_id='$user_id'"));
$siswa_id = $siswa['id'];
$tgl_ini = date('Y-m-d');

$cek_absen = mysqli_query($conn, "SELECT * FROM absensi WHERE siswa_id='$siswa_id' AND tanggal='$tgl_ini'");
$status_hari_ini = mysqli_fetch_assoc($cek_absen);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>

    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-4 md:p-8 space-y-6">
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <span class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-4 inline-block backdrop-blur-md">Selamat Datang</span>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2 uppercase italic"><?= $siswa['nama_lengkap'] ?></h1>
                    <p class="text-indigo-100 font-medium opacity-80 italic">"Disiplin adalah kunci utama menuju kesuksesan masa depan."</p>
                </div>
                <i class="fas fa-graduation-cap absolute -right-12 -bottom-12 text-[15rem] text-indigo-500 opacity-20 rotate-12"></i>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Status Kehadiran Hari Ini</h4>
                    
                    <?php if($status_hari_ini): ?>
                        <div class="space-y-4">
                            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-500 border-4 border-emerald-100">
                                <i class="fas fa-check-double text-4xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-slate-800 uppercase tracking-tight">SUDAH ABSEN</p>
                                <p class="text-sm font-bold text-slate-400 mt-1 uppercase tracking-widest">Pukul: <?= date('H:i', strtotime($status_hari_ini['jam_masuk'])) ?> WIB</p>
                            </div>
                            <div class="bg-emerald-500 text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest inline-block shadow-lg shadow-emerald-200">Terverifikasi</div>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <div class="w-24 h-24 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-rose-500 border-4 border-rose-100 animate-pulse">
                                <i class="fas fa-fingerprint text-4xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-slate-800 uppercase tracking-tight">BELUM ABSEN</p>
                                <p class="text-xs font-bold text-slate-400 mt-1">Silakan lakukan scan QR di depan kelas</p>
                            </div>
                            <a href="scan.php" class="inline-block bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                                <i class="fas fa-camera mr-2"></i> SCAN SEKARANG
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 border-b border-slate-50 pb-4">Informasi Akun Siswa</h4>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <span class="text-xs font-black text-slate-500 uppercase tracking-widest">NIS / Username</span>
                            </div>
                            <span class="font-black text-slate-800 tracking-wider"><?= $siswa['nis'] ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all">
                                    <i class="fas fa-school"></i>
                                </div>
                                <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Kelas & Jurusan</span>
                            </div>
                            <span class="font-black text-slate-800 uppercase italic"><?= $siswa['kelas_jurusan'] ?></span>
                        </div>

                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Status Akun</span>
                            </div>
                            <span class="text-[10px] font-black bg-emerald-50 text-emerald-600 px-3 py-1 rounded-lg uppercase border border-emerald-100">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>