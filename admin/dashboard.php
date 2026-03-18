<?php
include '../include/config.php';

// Proteksi Halaman
if ($_SESSION['role'] != 'admin') { 
    header("Location: ../login.php"); 
    exit; 
}

// Mengambil total data untuk statistik
$total_guru = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM guru"));
$total_siswa = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM siswa"));
$total_akun = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">
    
    <?php include '../include/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6">
            <div class="bg-indigo-900 rounded-3xl p-8 text-white mb-8 shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, Administrator!</h1>
                    <p class="text-indigo-200">Kelola master data guru dan sistem absensi dari panel ini.</p>
                </div>
                <i class="fas fa-cogs absolute -right-10 -bottom-10 text-9xl text-indigo-700 opacity-50"></i>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Total Guru</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $total_guru ?></h3>
                    </div>
                    <div class="bg-indigo-100 p-4 rounded-full text-indigo-600">
                        <i class="fas fa-chalkboard-teacher text-2xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Total Siswa</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $total_siswa ?></h3>
                    </div>
                    <div class="bg-green-100 p-4 rounded-full text-green-600">
                        <i class="fas fa-user-graduate text-2xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Total Akun Sistem</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $total_akun ?></h3>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-full text-yellow-600">
                        <i class="fas fa-users-cog text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border">
                <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-bolt text-yellow-500 mr-2"></i>Aksi Cepat</h3>
                <div class="flex gap-4">
                    <a href="data_guru.php" class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-lg font-semibold hover:bg-indigo-100 transition">
                        <i class="fas fa-plus mr-2"></i> Tambah Guru Baru
                    </a>
                </div>
            </div>
        </main>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>