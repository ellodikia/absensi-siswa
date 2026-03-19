<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$jml_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$siswa_query = mysqli_query($conn, "SELECT id, nama_lengkap FROM siswa ORDER BY nama_lengkap ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Bulanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        <main class="p-4 md:p-8">
            <h1 class="text-2xl font-black text-slate-800 mb-8 uppercase tracking-tight">Rekap Bulanan</h1>
            
            <form action="" method="GET" class="bg-white p-6 rounded-[2rem] shadow-sm mb-8 flex flex-wrap gap-4 items-end border border-slate-100">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Bulan</label>
                    <select name="bulan" class="w-full px-6 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        <?php for($m=1; $m<=12; $m++): $v=sprintf('%02d',$m); ?>
                            <option value="<?= $v ?>" <?= $bulan == $v ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Tahun</label>
                    <select name="tahun" class="w-full px-6 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                        <?php for($t=date('Y')-1; $t<=date('Y')+1; $t++): ?>
                            <option value="<?= $t ?>" <?= $tahun == $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Cari</button>
            </form>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-[10px] border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-left font-black uppercase text-slate-400 sticky left-0 bg-slate-50 z-10 border-r border-slate-100">Nama Siswa</th>
                                <?php for($d=1; $d<=$jml_hari; $d++): ?>
                                    <th class="p-2 text-center text-slate-400 font-black"><?= $d ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($s = mysqli_fetch_assoc($siswa_query)): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-black text-slate-700 uppercase sticky left-0 bg-white z-10 border-r border-slate-100 whitespace-nowrap"><?= $s['nama_lengkap'] ?></td>
                                <?php for($d=1; $d<=$jml_hari; $d++): 
                                    $tgl = "$tahun-$bulan-".sprintf('%02d',$d);
                                    $q = mysqli_query($conn, "SELECT status FROM absensi WHERE siswa_id='".$s['id']."' AND tanggal='$tgl'");
                                    $res = mysqli_fetch_assoc($q);
                                    $status = $res['status'] ?? '';
                                    $color = ['hadir'=>'text-emerald-500','sakit'=>'text-amber-500','izin'=>'text-blue-500','alpha'=>'text-rose-500'];
                                ?>
                                    <td class="p-2 text-center font-black <?= $color[$status] ?? 'text-slate-200' ?>">
                                        <?= $status ? strtoupper(substr($status, 0, 1)) : '-' ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>