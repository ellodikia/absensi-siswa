<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex flex-col md:flex-row min-h-screen">

    <div class="w-full md:w-64 bg-indigo-900 text-white p-6">
        <h1 class="text-2xl font-bold mb-8"><i class="fas fa-user-check mr-2"></i>QR-Absen</h1>
        <nav class="space-y-4">
            <a href="#" class="block py-2.5 px-4 rounded transition bg-indigo-700"><i class="fas fa-home mr-2"></i>Dashboard</a>
            <a href="#" class="block py-2.5 px-4 rounded transition hover:bg-indigo-800"><i class="fas fa-users mr-2"></i>Data Siswa</a>
            <a href="#" class="block py-2.5 px-4 rounded transition hover:bg-indigo-800"><i class="fas fa-file-alt mr-2"></i>Laporan</a>
            <a href="logout.php" class="block py-2.5 px-4 rounded transition hover:bg-red-600 mt-10"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </nav>
    </div>

    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-semibold text-gray-800">Selamat Datang, Guru Mapel</h2>
            <div class="text-sm text-gray-500"><?php echo date('d F Y'); ?></div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-gray-500">Total Siswa</p>
                <h3 class="text-3xl font-bold">32</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-gray-500">Hadir Hari Ini</p>
                <h3 class="text-3xl font-bold">28</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
                <p class="text-gray-500">Izin/Sakit</p>
                <h3 class="text-3xl font-bold">4</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Daftar Siswa Kelas X-RPL</h3>
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                    <i class="fas fa-plus mr-1"></i> Tambah Siswa
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Kelas</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4">Budi Santoso</td>
                            <td class="px-6 py-4">X RPL 1</td>
                            <td class="px-6 py-4 space-x-2">
                                <button class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                                <button class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">Set Sakit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>