<?php
include '../include/config.php';

// Proteksi Halaman
if ($_SESSION['role'] != 'siswa') { 
    header("Location: ../login.php"); 
    exit; 
}

// Ambil ID Siswa berdasarkan User ID yang login
$user_id = $_SESSION['user_id'];
$query_siswa = mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$user_id'");
$data_siswa = mysqli_fetch_assoc($query_siswa);
$siswa_id = $data_siswa['id'];

// Ambil riwayat absen 30 hari terakhir
$query_riwayat = "SELECT * FROM absensi WHERE siswa_id = '$siswa_id' ORDER BY tanggal DESC LIMIT 30";
$result_riwayat = mysqli_query($conn, $query_riwayat);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Absensi - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">
    
    <?php include '../include/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-history mr-2 text-indigo-600"></i>Riwayat Kehadiran (30 Hari Terakhir)</h1>

            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Tanggal</th>
                                <th class="px-6 py-4 font-semibold">Jam Scan</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php 
                            if(mysqli_num_rows($result_riwayat) > 0):
                                while($row = mysqli_fetch_assoc($result_riwayat)): 
                                    // Format tampilan status
                                    $status = $row['status'];
                                    $badge_color = '';
                                    if($status == 'hadir') $badge_color = 'bg-green-100 text-green-700';
                                    elseif($status == 'sakit') $badge_color = 'bg-yellow-100 text-yellow-700';
                                    elseif($status == 'izin') $badge_color = 'bg-blue-100 text-blue-700';
                                    else $badge_color = 'bg-red-100 text-red-700';
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-800 font-medium">
                                    <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-mono">
                                    <?= $row['jam_masuk'] != '00:00:00' ? $row['jam_masuk'] : '-' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $badge_color ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    <?= !empty($row['keterangan']) ? $row['keterangan'] : '-' ?>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-folder-open text-3xl mb-3 text-gray-300 block"></i>
                                    Belum ada data riwayat absensi.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>