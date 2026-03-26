<?php
include '../include/config.php';

if ($_SESSION['role'] != 'guru') { 
    header("Location: ../login.php"); 
    exit; 
}

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$kelas_filter = $_GET['kelas'] ?? ''; 

$jml_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$list_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa ORDER BY kelas_jurusan ASC");

$query_str = "SELECT id, nama_lengkap, kelas_jurusan FROM siswa";
if ($kelas_filter != '') {
    $query_str .= " WHERE kelas_jurusan = '" . mysqli_real_escape_string($conn, $kelas_filter) . "'";
}
$query_str .= " ORDER BY nama_lengkap ASC";
$siswa_query = mysqli_query($conn, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Bulanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 flex overflow-x-hidden">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen max-w-full overflow-hidden">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-4 md:p-8 space-y-6 max-w-full">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Rekap Bulanan</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        <i class="far fa-calendar-alt mr-1"></i> <?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?>
                    </p>
                </div>
                
                <div class="flex flex-wrap gap-3 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Hadir
                    </div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Sakit
                    </div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-blue-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Izin
                    </div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-rose-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Alpha
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                <form action="" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div class="sm:col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Kelas</label>
                        <select name="kelas" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Kelas</option>
                            <?php while($k = mysqli_fetch_assoc($list_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>" <?= $kelas_filter == $k['kelas_jurusan'] ? 'selected' : '' ?>><?= $k['kelas_jurusan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Bulan</label>
                        <select name="bulan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                            <?php for($m=1; $m<=12; $m++): $v=sprintf('%02d',$m); ?>
                                <option value="<?= $v ?>" <?= $bulan == $v ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Tahun</label>
                        <select name="tahun" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                            <?php for($t=date('Y')-1; $t<=date('Y')+1; $t++): ?>
                                <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md active:scale-95">Cari</button>
                    <a href="export_excel.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&kelas=<?= urlencode($kelas_filter) ?>" class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-md inline-flex items-center justify-center gap-2 active:scale-95">
                        <i class="fas fa-file-excel"></i> <span>Excel</span>
                    </a>
                </form>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="custom-scrollbar overflow-x-auto relative">
                    <table class="w-full text-[10px] border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-6 py-4 text-left font-black uppercase text-slate-400 sticky left-0 bg-slate-50 z-30 border-b border-r border-slate-200 min-w-[150px] shadow-[2px_0_5px_rgba(0,0,0,0.03)]">Nama Siswa</th>
                                <?php for($d=1; $d<=$jml_hari; $d++): ?>
                                    <th class="p-2 text-center text-slate-400 font-black border-b border-r border-slate-100 min-w-[35px]"><?= $d ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php while($s = mysqli_fetch_assoc($siswa_query)): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-3 font-black text-slate-700 uppercase sticky left-0 bg-white z-20 border-r border-slate-200 whitespace-nowrap shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                    <div class="truncate max-w-[140px]"><?= $s['nama_lengkap'] ?></div>
                                </td>
                                <?php for($d=1; $d<=$jml_hari; $d++): 
                                    $tgl = "$tahun-$bulan-".sprintf('%02d',$d);
                                    $q = mysqli_query($conn, "SELECT status FROM absensi WHERE siswa_id='".$s['id']."' AND tanggal='$tgl'");
                                    $res = mysqli_fetch_assoc($q);
                                    $status = $res['status'] ?? '';
                                    $color = [
                                        'hadir' => 'bg-emerald-50 text-emerald-600',
                                        'sakit' => 'bg-amber-50 text-amber-600',
                                        'izin'  => 'bg-blue-50 text-blue-600',
                                        'alpha' => 'bg-rose-50 text-rose-600'
                                    ];
                                    $active = $color[$status] ?? 'text-slate-200';
                                ?>
                                    <td class="p-1 text-center border-r border-slate-50 <?= $active ?>">
                                        <div class="w-7 h-7 flex items-center justify-center rounded-lg font-black mx-auto">
                                            <?= $status ? strtoupper(substr($status, 0, 1)) : '-' ?>
                                        </div>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        
        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>