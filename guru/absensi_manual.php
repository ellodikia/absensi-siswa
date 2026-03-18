<?php
include '../include/config.php';

// Proteksi Halaman
if ($_SESSION['role'] != 'guru') { 
    header("Location: ../login.php"); 
    exit; 
}

// Tangkap parameter filter
$tgl_pilih = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');
$kelas_pilih = isset($_GET['kelas']) ? $_GET['kelas'] : '';

// Proses Update Status Manual
if (isset($_POST['update_status'])) {
    $siswa_id = $_POST['siswa_id'];
    $status = $_POST['status'];
    $tgl = $_POST['tanggal'];
    $ket = escape($_POST['keterangan']);
    
    // Jika diubah menjadi hadir, catat jam sekarang. Jika tidak, kosongkan/default.
    $jam_masuk = ($status == 'hadir') ? date('H:i:s') : '00:00:00';

    // Cek apakah data absen sudah ada di tanggal tersebut
    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id='$siswa_id' AND tanggal='$tgl'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Update jika data sudah ada
        mysqli_query($conn, "UPDATE absensi SET status='$status', keterangan='$ket' WHERE siswa_id='$siswa_id' AND tanggal='$tgl'");
    } else {
        // Insert baru jika siswa belum absen sama sekali
        mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, status, keterangan, jam_masuk) 
                             VALUES ('$siswa_id', '$tgl', '$status', '$ket', '$jam_masuk')");
    }
    $success = "Status absensi berhasil diperbarui!";
}

// Ambil daftar kelas unik untuk dropdown filter
$query_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa ORDER BY kelas_jurusan ASC");

// Ambil data siswa beserta status absennya di tanggal terpilih
$query_tampil = "SELECT s.id, s.nama_lengkap, s.kelas_jurusan, s.nis, a.status, a.keterangan 
                 FROM siswa s 
                 LEFT JOIN absensi a ON s.id = a.siswa_id AND a.tanggal = '$tgl_pilih'";

// Jika guru memfilter berdasarkan kelas
if (!empty($kelas_pilih)) {
    $query_tampil .= " WHERE s.kelas_jurusan = '$kelas_pilih'";
}
$query_tampil .= " ORDER BY s.nama_lengkap ASC";

$result = mysqli_query($conn, $query_tampil);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Absensi Manual - Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">
    
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-edit mr-2 text-indigo-600"></i>Kelola Absensi Manual</h1>

            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-200">
                <form action="" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Tanggal</label>
                        <input type="date" name="tgl" value="<?= $tgl_pilih ?>" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Kelas</label>
                        <select name="kelas" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                            <option value="">-- Semua Kelas --</option>
                            <?php while($k = mysqli_fetch_assoc($query_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>" <?= $kelas_pilih == $k['kelas_jurusan'] ? 'selected' : '' ?>>
                                    <?= $k['kelas_jurusan'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 font-semibold shadow-sm w-full">
                            Tampilkan Data
                        </button>
                    </div>
                </form>
            </div>

            <?php if(isset($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Data Siswa</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase text-center">Status Saat Ini</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Aksi Manual (Ubah Status)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php 
                            if(mysqli_num_rows($result) > 0):
                                while($row = mysqli_fetch_assoc($result)): 
                                    $s = $row['status'] ?? 'belum absen';
                                    
                                    // Warna Badge Status
                                    $bg_color = 'bg-gray-100 text-gray-600';
                                    if($s == 'hadir') $bg_color = 'bg-green-100 text-green-700';
                                    elseif($s == 'sakit') $bg_color = 'bg-yellow-100 text-yellow-700';
                                    elseif($s == 'izin') $bg_color = 'bg-blue-100 text-blue-700';
                                    elseif($s == 'alpha') $bg_color = 'bg-red-100 text-red-700';
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800"><?= $row['nama_lengkap'] ?></div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <span class="font-mono bg-gray-100 px-2 py-0.5 rounded"><?= $row['nis'] ?></span> &bull; <?= $row['kelas_jurusan'] ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border <?= $bg_color ?>">
                                        <?= $s ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="" method="POST" class="flex flex-col lg:flex-row items-start lg:items-center gap-2">
                                        <input type="hidden" name="siswa_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="tanggal" value="<?= $tgl_pilih ?>">
                                        
                                        <select name="status" class="text-sm border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                                            <option value="hadir" <?= $s == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                            <option value="sakit" <?= $s == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                            <option value="izin" <?= $s == 'izin' ? 'selected' : '' ?>>Izin</option>
                                            <option value="alpha" <?= $s == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                                        </select>

                                        <input type="text" name="keterangan" value="<?= $row['keterangan'] ?>" placeholder="Keterangan (Opsional)" class="text-sm border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500 w-full lg:w-48">
                                        
                                        <button type="submit" name="update_status" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-semibold shadow-sm w-full lg:w-auto">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users-slash text-4xl mb-3 text-gray-300 block"></i>
                                    Tidak ada data siswa untuk kelas ini.
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