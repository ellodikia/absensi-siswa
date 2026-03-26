<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$tgl_filter = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');
$kelas_filter = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query_absen = "SELECT absensi.*, siswa.nama_lengkap, siswa.kelas_jurusan, siswa.nis FROM absensi JOIN siswa ON absensi.siswa_id = siswa.id WHERE absensi.tanggal = '$tgl_filter'";
if (!empty($kelas_filter)) { $query_absen .= " AND siswa.kelas_jurusan = '" . mysqli_real_escape_string($conn, $kelas_filter) . "'"; }
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
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print { #sidebar, header, .no-print { display: none !important; } main { margin: 0 !important; width: 100% !important; } }
        .custom-scrollbar::-webkit-scrollbar { height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50 flex overflow-x-hidden">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen max-w-full overflow-hidden">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-4 md:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Laporan Harian</h1>
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="?tgl=<?= $tgl_filter ?>&kelas=<?= $kelas_filter ?>&status=<?= $status_filter ?>&export=excel" class="flex-1 sm:flex-none bg-emerald-600 text-white px-5 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </a>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 no-print">
                <form action="" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Tanggal</label>
                        <input type="date" name="tgl" value="<?= $tgl_filter ?>" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Kelas</label>
                        <select name="kelas" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                            <option value="">Semua Kelas</option>
                            <?php mysqli_data_seek($query_kelas, 0); while($k = mysqli_fetch_assoc($query_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>" <?= $kelas_filter == $k['kelas_jurusan'] ? 'selected' : '' ?>><?= $k['kelas_jurusan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Status</label>
                        <select name="status" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                            <option value="">Semua Status</option>
                            <option value="hadir" <?= $status_filter == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                            <option value="sakit" <?= $status_filter == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                            <option value="izin" <?= $status_filter == 'izin' ? 'selected' : '' ?>>Izin</option>
                            <option value="alpha" <?= $status_filter == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white h-[46px] rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md active:scale-95">
                        <i class="fas fa-filter mr-2"></i> Cari
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar relative">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em]">
                                <th class="px-8 py-6 sticky left-0 bg-slate-50 z-30 border-b border-r border-slate-100 min-w-[200px] shadow-[2px_0_5px_rgba(0,0,0,0.02)]">Nama Siswa</th>
                                <th class="px-8 py-6 border-b border-r border-slate-100 min-w-[150px]">Kelas</th>
                                <th class="px-8 py-6 border-b border-r border-slate-100 min-w-[120px]">Jam Masuk</th>
                                <th class="px-8 py-6 border-b border-slate-100 min-w-[150px] text-center">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if(mysqli_num_rows($result) > 0): while($row = mysqli_fetch_assoc($result)): 
                                $colors = [
                                    'hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'sakit' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'izin'  => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'alpha' => 'bg-rose-50 text-rose-600 border-rose-100'
                                ];
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-6 sticky left-0 bg-white z-20 border-r border-slate-100 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                    <p class="text-sm font-black text-slate-800 uppercase tracking-tight"><?= $row['nama_lengkap'] ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1"><?= $row['nis'] ?></p>
                                </td>
                                <td class="px-8 py-6 text-xs font-bold text-slate-500 whitespace-nowrap italic"><?= $row['kelas_jurusan'] ?></td>
                                <td class="px-8 py-6 font-black text-sm text-slate-800 tracking-tighter whitespace-nowrap">
                                    <i class="far fa-clock text-slate-300 mr-2"></i><?= ($row['jam_masuk'] != '00:00:00') ? date('H:i', strtotime($row['jam_masuk'])) : '--:--' ?>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-block min-w-[100px] px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border <?= $colors[$row['status']] ?? 'bg-slate-50 text-slate-400' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="px-8 py-24 text-center font-bold text-slate-300 italic uppercase tracking-widest text-xs">Data absensi kosong</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden p-3 bg-slate-50 border-t border-slate-100 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><i class="fas fa-arrows-left-right mr-1"></i> Geser ke kanan untuk detail</p>
                </div>
            </div>
        </main>
        
        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>