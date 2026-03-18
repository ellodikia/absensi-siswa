<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

// Tangkap parameter filter (Jika kosong, gunakan default)
$tgl_filter = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');
$kelas_filter = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Susun Query Dasar
$query_absen = "SELECT absensi.*, siswa.nama_lengkap, siswa.kelas_jurusan, siswa.nis 
                FROM absensi 
                JOIN siswa ON absensi.siswa_id = siswa.id 
                WHERE absensi.tanggal = '$tgl_filter'";

// Jika filter kelas dipilih
if (!empty($kelas_filter)) { $query_absen .= " AND siswa.kelas_jurusan = '$kelas_filter'"; }

// Jika filter status dipilih
if (!empty($status_filter)) { $query_absen .= " AND absensi.status = '$status_filter'"; }

$query_absen .= " ORDER BY absensi.jam_masuk DESC";
$result = mysqli_query($conn, $query_absen);

// ==========================================
// PROSES EXPORT EXCEL (FITUR BARU)
// ==========================================
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Absensi_".$tgl_filter.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    ?>
    <table border="1">
        <thead>
            <tr>
                <th colspan="5" style="font-size: 16px; font-weight: bold; padding: 10px;">Laporan Kehadiran Siswa - <?= date('d M Y', strtotime($tgl_filter)) ?></th>
            </tr>
            <tr>
                <th style="background-color: #f3f4f6;">NIS</th>
                <th style="background-color: #f3f4f6;">Nama Siswa</th>
                <th style="background-color: #f3f4f6;">Kelas</th>
                <th style="background-color: #f3f4f6;">Jam Masuk</th>
                <th style="background-color: #f3f4f6;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){ 
                    echo "<tr>";
                    echo "<td>'".$row['nis']."</td>"; // Tanda petik agar NIS tidak diubah jadi format angka (scientific) oleh Excel
                    echo "<td>".$row['nama_lengkap']."</td>";
                    echo "<td>".$row['kelas_jurusan']."</td>";
                    echo "<td>".($row['jam_masuk'] != '00:00:00' ? $row['jam_masuk'] : '-')."</td>";
                    echo "<td>".strtoupper($row['status'])."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' align='center'>Tidak ada data absensi yang sesuai filter.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <?php
    exit; // Menghentikan script agar desain web di bawahnya tidak ikut masuk ke file Excel
}

// Ambil daftar kelas unik untuk dropdown filter
$query_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa ORDER BY kelas_jurusan ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Sembunyikan elemen tertentu saat dicetak (Ctrl+P) */
        @media print {
            #sidebar, header, .no-print { display: none !important; }
            main { padding: 0 !important; margin: 0 !important; width: 100% !important; }
            .print-title { display: block !important; }
        }
    </style>
</head>
<body class="bg-gray-100 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 hidden print-title text-center uppercase">Laporan Absensi Siswa <br> Tanggal: <?= date('d F Y', strtotime($tgl_filter)) ?></h1>

            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 no-print border border-gray-200">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4"><i class="fas fa-filter mr-2"></i>Filter Laporan</h3>
                
                <form action="" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
                    <div class="w-full lg:w-auto">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tgl" value="<?= $tgl_filter ?>" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                    </div>
                    
                    <div class="w-full lg:w-auto">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas / Jurusan</label>
                        <select name="kelas" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                            <option value="">-- Semua Kelas --</option>
                            <?php while($k = mysqli_fetch_assoc($query_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>" <?= $kelas_filter == $k['kelas_jurusan'] ? 'selected' : '' ?>>
                                    <?= $k['kelas_jurusan'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="w-full lg:w-auto">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Kehadiran</label>
                        <select name="status" class="w-full border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                            <option value="">-- Semua Status --</option>
                            <option value="hadir" <?= $status_filter == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                            <option value="sakit" <?= $status_filter == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                            <option value="izin" <?= $status_filter == 'izin' ? 'selected' : '' ?>>Izin</option>
                            <option value="alpha" <?= $status_filter == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2 w-full lg:w-auto mt-2 lg:mt-0">
                        <button type="submit" class="flex-1 lg:flex-none bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 font-semibold shadow-sm transition">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="laporan.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center justify-center">
                            Reset
                        </a>
                        <button type="button" onclick="window.print()" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 font-semibold shadow-sm transition">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <button type="submit" name="export" value="excel" class="bg-emerald-600 text-white px-5 py-2 rounded-lg hover:bg-emerald-700 font-semibold shadow-sm transition flex items-center justify-center">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-600 border-b">
                            <tr>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">NIS</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Nama Siswa</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Kelas</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Jam Masuk</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php 
                            if(mysqli_num_rows($result) > 0):
                                // Pindahkan pointer result ke baris pertama (karena sudah dipakai di loop Export Excel di atas)
                                mysqli_data_seek($result, 0); 
                                while($row = mysqli_fetch_assoc($result)): 
                                    $s = $row['status'];
                                    $colors = ['hadir'=>'bg-green-100 text-green-700', 'sakit'=>'bg-yellow-100 text-yellow-700', 'izin'=>'bg-blue-100 text-blue-700', 'alpha'=>'bg-red-100 text-red-700'];
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-gray-600"><?= $row['nis'] ?></td>
                                <td class="px-6 py-4 font-bold text-gray-800"><?= $row['nama_lengkap'] ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= $row['kelas_jurusan'] ?></td>
                                <td class="px-6 py-4 font-mono"><?= $row['jam_masuk'] != '00:00:00' ? $row['jam_masuk'] : '-' ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border <?= $colors[$s] ?>">
                                        <?= $s ?>
                                    </span>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-search text-4xl mb-3 text-gray-300 block"></i>
                                    Tidak ada data absensi yang sesuai dengan filter.
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