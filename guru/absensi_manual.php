<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$tgl_pilih = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');

// Proses Update Status
if (isset($_POST['update_status'])) {
    $siswa_id = $_POST['siswa_id'];
    $status = $_POST['status'];
    $tgl = $_POST['tanggal'];
    $ket = escape($_POST['keterangan']);

    // Cek apakah data absen sudah ada
    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id='$siswa_id' AND tanggal='$tgl'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Update jika sudah ada
        mysqli_query($conn, "UPDATE absensi SET status='$status', keterangan='$ket' WHERE siswa_id='$siswa_id' AND tanggal='$tgl'");
    } else {
        // Insert baru jika belum ada (misal siswa tidak scan tapi guru mau set Sakit)
        mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, status, keterangan, jam_masuk) VALUES ('$siswa_id', '$tgl', '$status', '$ket', '00:00:00')");
    }
    $success = "Status absensi berhasil diperbarui!";
}

// Ambil semua siswa dan status absen mereka di tanggal terpilih
$query = "SELECT s.id, s.nama_lengkap, s.kelas_jurusan, a.status, a.keterangan 
          FROM siswa s 
          LEFT JOIN absensi a ON s.id = a.siswa_id AND a.tanggal = '$tgl_pilih'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Absensi Manual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Kelola Kehadiran Siswa</h1>
                <form action="" method="GET" class="flex items-center gap-2">
                    <input type="date" name="tgl" value="<?= $tgl_pilih ?>" onchange="this.form.submit()" class="border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                </form>
            </div>

            <?php if(isset($success)): ?>
                <div class="bg-green-500 text-white p-4 rounded-xl mb-6 shadow-lg animate-bounce">
                    <i class="fas fa-check-circle mr-2"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="px-6 py-4">Nama Siswa</th>
                            <th class="px-6 py-4">Status Saat Ini</th>
                            <th class="px-6 py-4 text-center">Aksi Manual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800"><?= $row['nama_lengkap'] ?></div>
                                <div class="text-xs text-gray-400"><?= $row['kelas_jurusan'] ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                    $s = $row['status'] ?? 'alpha';
                                    $colors = ['hadir'=>'bg-green-100 text-green-700', 'sakit'=>'bg-yellow-100 text-yellow-700', 'izin'=>'bg-blue-100 text-blue-700', 'alpha'=>'bg-red-100 text-red-700'];
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $colors[$s] ?>">
                                    <?= $s ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form action="" method="POST" class="flex items-center justify-center gap-2">
                                    <input type="hidden" name="siswa_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="tanggal" value="<?= $tgl_pilih ?>">
                                    <select name="status" class="text-sm border rounded p-1 outline-none">
                                        <option value="hadir">Hadir</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="izin">Izin</option>
                                        <option value="alpha">Alpha</option>
                                    </select>
                                    <button type="submit" name="update_status" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>