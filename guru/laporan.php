<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

// Filter default (Hari ini)
$tgl_filter = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');

$query_absen = "SELECT absensi.*, siswa.nama_lengkap, siswa.kelas_jurusan 
                FROM absensi 
                JOIN siswa ON absensi.siswa_id = siswa.id 
                WHERE absensi.tanggal = '$tgl_filter'
                ORDER BY absensi.jam_masuk DESC";
$result = mysqli_query($conn, $query_absen);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-64 flex flex-col">
        <?php include '../include/header_nav.php'; ?>
        <main class="p-6">
            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Tanggal</label>
                    <form action="" method="GET" class="flex gap-2">
                        <input type="date" name="tgl" value="<?= $tgl_filter ?>" class="border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Filter</button>
                    </form>
                </div>
                <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg ml-auto">
                    <i class="fas fa-print mr-2"></i> Cetak Laporan
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-4">Nama Siswa</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="px-6 py-4 font-medium"><?= $row['nama_lengkap'] ?></td>
                            <td class="px-6 py-4"><?= $row['kelas_jurusan'] ?></td>
                            <td class="px-6 py-4"><?= $row['jam_masuk'] ?></td>
                            <td class="px-6 py-4">
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Hadir</span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>