<?php
include '../include/config.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

$total_guru = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM guru"));
$total_siswa = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM siswa"));
$total_akun = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>

    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-4 md:p-8">
            <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white mb-8 shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-black mb-2 tracking-tight uppercase">Administrator</h1>
                </div>
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <i class="fas fa-shield-alt text-9xl"></i>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-xl transition-all group">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Guru</p>
                        <h3 class="text-4xl font-black text-slate-800"><?= $total_guru ?></h3>
                    </div>
                    <div class="bg-indigo-50 p-5 rounded-2xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fas fa-chalkboard-teacher text-2xl"></i>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-xl transition-all group">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Siswa</p>
                        <h3 class="text-4xl font-black text-slate-800"><?= $total_siswa ?></h3>
                    </div>
                    <div class="bg-emerald-50 p-5 rounded-2xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <i class="fas fa-user-graduate text-2xl"></i>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-xl transition-all group">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Akun Aktif</p>
                        <h3 class="text-4xl font-black text-slate-800"><?= $total_akun ?></h3>
                    </div>
                    <div class="bg-amber-50 p-5 rounded-2xl text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all">
                        <i class="fas fa-users-cog text-2xl"></i>
                    </div>
                </div>
            </div>
            
        </main>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>