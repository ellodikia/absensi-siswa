<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$tgl_filter = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');
$kelas_filter = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query_absen = "SELECT absensi.*, siswa.nama_lengkap, siswa.kelas_jurusan, siswa.nis FROM absensi JOIN siswa ON absensi.siswa_id = siswa.id WHERE absensi.tanggal = '$tgl_filter'";
if (!empty($kelas_filter)) { $query_absen .= " AND siswa.kelas_jurusan = '$kelas_filter'"; }
if (!empty($status_filter)) { $query_absen .= " AND absensi.status = '$status_filter'"; }
$query_absen .= " ORDER BY absensi.jam_masuk DESC";
$result = mysqli_query($conn, $query_absen);

if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Absensi_".$tgl_filter.".xls");
    echo "<table border='1'><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Jam</th><th>Status</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>'".$row['nis']."</td><td>".$row['nama_lengkap']."</td><td>".$row['kelas_jurusan']."</td><td>".$row['jam_masuk']."</td><td>".strtoupper($row['status'])."</td></tr>";
    }
    echo "</table>";
    exit;
}

$query_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa ORDER BY kelas_jurusan ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>@media print { #sidebar, header, .no-print { display: none !important; } main { margin: 0 !important; width: 100% !important; } }</style>
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        <main class="p-4 md:p-8">
            <div class="bg-white p-6 md:p-10 rounded-[2.5rem] shadow-sm border border-slate-100 mb-8 no-print">
                <form action="" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Tanggal</label>
                        <input type="date" name="tgl" value="<?= $tgl_filter ?>" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Kelas</label>
                        <select name="kelas" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                            <option value="">Semua</option>
                            <?php while($k = mysqli_fetch_assoc($query_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>" <?= $kelas_filter == $k['kelas_jurusan'] ? 'selected' : '' ?>><?= $k['kelas_jurusan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Status</label>
                        <select name="status" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                            <option value="">Semua</option>
                            <option value="hadir" <?= $status_filter == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                            <option value="sakit" <?= $status_filter == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                            <option value="izin" <?= $status_filter == 'izin' ? 'selected' : '' ?>>Izin</option>
                            <option value="alpha" <?= $status_filter == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white h-14 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"><i class="fas fa-search"></i></button>
                        <button type="button" onclick="window.print()" class="w-14 h-14 bg-slate-100 text-slate-600 rounded-2xl hover:bg-slate-200 transition-all"><i class="fas fa-print"></i></button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em] border-b border-slate-100">
                                <th class="px-8 py-6">Nama Siswa</th>
                                <th class="px-8 py-6">Kelas</th>
                                <th class="px-8 py-6">Jam</th>
                                <th class="px-8 py-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if(mysqli_num_rows($result) > 0): while($row = mysqli_fetch_assoc($result)): 
                                $colors = ['hadir'=>'bg-emerald-50 text-emerald-600', 'sakit'=>'bg-amber-50 text-amber-600', 'izin'=>'bg-blue-50 text-blue-600', 'alpha'=>'bg-rose-50 text-rose-600'];
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-800 uppercase tracking-tight"><?= $row['nama_lengkap'] ?></p>
                                    <p class="text-xs font-bold text-slate-400 mt-1"><?= $row['nis'] ?></p>
                                </td>
                                <td class="px-8 py-6 text-xs font-bold text-slate-500"><?= $row['kelas_jurusan'] ?></td>
                                <td class="px-8 py-6 font-black text-sm text-slate-800 tracking-tighter"><?= $row['jam_masuk'] != '00:00:00' ? $row['jam_masuk'] : '--:--' ?></td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border <?= $colors[$row['status']] ?>"><?= $row['status'] ?></span>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="px-8 py-20 text-center font-bold text-slate-300 italic uppercase tracking-widest text-sm">Data tidak ditemukan</td></tr>
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