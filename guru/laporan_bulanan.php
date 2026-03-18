<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$siswa_query = mysqli_query($conn, "SELECT id, nama_lengkap FROM siswa ORDER BY nama_lengkap ASC");

// ==========================================
// PROSES EXPORT EXCEL REKAP BULANAN
// ==========================================
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Rekap_Bulanan_".$tahun."_".$bulan.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    ?>
    <table border="1">
        <thead>
            <tr>
                <th colspan="<?= $jumlah_hari + 1 ?>" style="font-size: 16px; font-weight: bold; padding: 10px;">
                    Rekapitulasi Absensi Bulanan - Bulan: <?= $bulan ?> / <?= $tahun ?>
                </th>
            </tr>
            <tr>
                <th style="background-color: #f3f4f6;">Nama Siswa</th>
                <?php for($d=1; $d<=$jumlah_hari; $d++): ?>
                    <th style="background-color: #f3f4f6; text-align: center;"><?= $d ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php while($s = mysqli_fetch_assoc($siswa_query)): ?>
            <tr>
                <td><?= $s['nama_lengkap'] ?></td>
                <?php 
                for($d=1; $d<=$jumlah_hari; $d++): 
                    $tgl_cek = "$tahun-$bulan-" . sprintf('%02d', $d);
                    $cek_abs = mysqli_query($conn, "SELECT status FROM absensi WHERE siswa_id='".$s['id']."' AND tanggal='$tgl_cek'");
                    $data_abs = mysqli_fetch_assoc($cek_abs);
                    $status = $data_abs['status'] ?? '';
                    
                    // Singkatan status: H (Hadir), S (Sakit), I (Izin), A (Alpha), - (Kosong)
                    $inisial = $status ? strtoupper(substr($status, 0, 1)) : '-';
                ?>
                    <td align="center"><?= $inisial ?></td>
                <?php endfor; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekap Bulanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-6">
            <h1 class="text-2xl font-bold mb-6">Rekap Absensi Bulanan</h1>
            
            <form action="" method="GET" class="bg-white p-6 rounded-xl shadow-sm mb-6 flex flex-col md:flex-row gap-4 items-end border border-gray-200">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                    <select name="bulan" class="border p-2 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?= sprintf('%02d', $m) ?>" <?= $bulan == $m ? 'selected' : '' ?>>
                                <?= date('F', mktime(0,0,0,$m,1)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                    <select name="tahun" class="border p-2 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                        <?php 
                        $tahun_sekarang = date('Y');
                        for($t=$tahun_sekarang-1; $t<=$tahun_sekarang+1; $t++): 
                        ?>
                            <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 font-semibold transition">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                    <button type="submit" name="export" value="excel" class="bg-emerald-600 text-white px-5 py-2 rounded-lg hover:bg-emerald-700 font-semibold transition">
                        <i class="fas fa-file-excel mr-1"></i> Download Excel
                    </button>
                </div>
            </form>

            <div class="bg-white p-4 rounded-xl shadow-sm overflow-x-auto border border-gray-200">
                <table class="w-full text-xs border-collapse border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2 sticky left-0 bg-gray-100 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Nama Siswa</th>
                            <?php for($d=1; $d<=$jumlah_hari; $d++): ?>
                                <th class="border p-1 w-8 text-center text-gray-600 font-semibold"><?= $d ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Pindahkan pointer karena sudah terpakai oleh Export Excel di atas
                        mysqli_data_seek($siswa_query, 0);
                        while($s = mysqli_fetch_assoc($siswa_query)): 
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border p-2 font-bold sticky left-0 bg-white shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] whitespace-nowrap"><?= $s['nama_lengkap'] ?></td>
                            <?php 
                            for($d=1; $d<=$jumlah_hari; $d++): 
                                $tgl_cek = "$tahun-$bulan-" . sprintf('%02d', $d);
                                $cek_abs = mysqli_query($conn, "SELECT status FROM absensi WHERE siswa_id='".$s['id']."' AND tanggal='$tgl_cek'");
                                $data_abs = mysqli_fetch_assoc($cek_abs);
                                $status = $data_abs['status'] ?? '';
                                
                                $bg = '';
                                if($status == 'hadir') $bg = 'bg-green-500 text-white shadow-inner';
                                elseif($status == 'sakit') $bg = 'bg-yellow-400 shadow-inner';
                                elseif($status == 'izin') $bg = 'bg-blue-400 text-white shadow-inner';
                                elseif($status == 'alpha') $bg = 'bg-red-500 text-white shadow-inner';
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