<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    $role = $_SESSION['role'];
    header("Location: $role/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raksana Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234f46e5' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 bg-pattern min-h-screen flex flex-col">

    <nav class="bg-white/80 backdrop-blur-md shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center space-x-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-xl text-indigo-900 tracking-tight">RAbsen</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center pt-20 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-block mb-4 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 font-semibold text-sm">
                <i class="fas fa-shield-alt mr-2"></i> Sistem Absensi Digital.
            </div>
            
            <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 tracking-tight mb-6 leading-tight">
                Absensi Digital <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                    Yayasan Pendidikan Raksana Medan
                </span>
            </h1>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="login.php" class="bg-indigo-600 text-white font-bold text-lg px-8 py-4 rounded-xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login ke Sistem
                </a>
            </div>

            <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Panel Admin</h3>
                    <p class="text-gray-600">Kelola master data guru, siswa, kelas/jurusan.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-xl flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Fitur Guru</h3>
                    <p class="text-gray-600">Tampilkan QR Code dinamis di kelas, kelola data siswa, dan cetak laporan rekap bulanan otomatis.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Akses Siswa</h3>
                    <p class="text-gray-600">Scan QR langsung dari kamera HP browser tanpa perlu install aplikasi. Pantau riwayat kehadiran mandiri.</p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'include/footer.php'; ?> 
</body>
</html>