<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$siswa_query = mysqli_query($conn, "SELECT id, nama_lengkap FROM siswa ORDER BY nama_lengkap ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekap Bulanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-6">
            <h1 class="text-2xl font-bold mb-6">Rekap Absensi Bulanan</h1>
            
            <form action="" method="GET" class="bg-white p-4 rounded-xl shadow-sm mb-6 flex gap-4">
                <select name="bulan" class="border p-2 rounded">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= sprintf('%02d', $m) ?>" <?= $bulan == $m ? 'selected' : '' ?>>
                            <?= date('F', mktime(0,0,0,$m,1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select name="tahun" class="border p-2 rounded">
                    <option value="2024" selected>2024</option>
                    <option value="2025">2025</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Cari</button>
            </form>

            <div class="bg-white p-4 rounded-xl shadow-sm overflow-x-auto">
                <table class="w-full text-xs border-collapse border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2 sticky left-0 bg-gray-100">Nama Siswa</th>
                            <?php for($d=1; $d<=$jumlah_hari; $d++): ?>
                                <th class="border p-1 w-8 text-center"><?= $d ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = mysqli_fetch_assoc($siswa_query)): ?>
                        <tr>
                            <td class="border p-2 font-bold sticky left-0 bg-white"><?= $s['nama_lengkap'] ?></td>
                            <?php 
                            for($d=1; $d<=$jumlah_hari; $d++): 
                                $tgl_cek = "$tahun-$bulan-" . sprintf('%02d', $d);
                                $cek_abs = mysqli_query($conn, "SELECT status FROM absensi WHERE siswa_id='".$s['id']."' AND tanggal='$tgl_cek'");
                                $data_abs = mysqli_fetch_assoc($cek_abs);
                                $status = $data_abs['status'] ?? '';
                                
                                $bg = '';
                                if($status == 'hadir') $bg = 'bg-green-500 text-white';
                                elseif($status == 'sakit') $bg = 'bg-yellow-400';
                                elseif($status == 'izin') $bg = 'bg-blue-400';
                                elseif($status == 'alpha') $bg = 'bg-red-500 text-white';
                            ?>
                                <td class="border p-1 text-center font-bold <?= $bg ?>">
                                    <?= $status ? strtoupper(substr($status, 0, 1)) : '-' ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>