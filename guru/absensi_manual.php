<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$tgl_pilih = $_GET['tgl'] ?? date('Y-m-d');
$kelas_pilih = $_GET['kelas'] ?? '';
$search_nama = $_GET['search'] ?? '';

if (isset($_POST['update'])) {
    $sid = $_POST['sid']; 
    $st = $_POST['status']; 
    $t = $_POST['tgl']; 
    $ket = mysqli_real_escape_string($conn, $_POST['ket']);
    $jam = ($st == 'hadir') ? date('H:i:s') : '00:00:00';

    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id='$sid' AND tanggal='$t'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE absensi SET status='$st', keterangan='$ket' WHERE siswa_id='$sid' AND tanggal='$t'");
    } else {
        mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, status, keterangan, jam_masuk) VALUES ('$sid', '$t', '$st', '$ket', '$jam')");
    }
    $success = "Absensi berhasil disimpan!";
}

$query_tampil = "SELECT s.id, s.nama_lengkap, s.kelas_jurusan, s.nis, a.status, a.keterangan 
                 FROM siswa s 
                 LEFT JOIN absensi a ON s.id = a.siswa_id AND a.tanggal = '$tgl_pilih' 
                 WHERE 1=1";

if ($kelas_pilih) $query_tampil .= " AND s.kelas_jurusan = '$kelas_pilih'";
if ($search_nama) $query_tampil .= " AND s.nama_lengkap LIKE '%$search_nama%'";

$result = mysqli_query($conn, $query_tampil . " ORDER BY s.nama_lengkap ASC");
$list_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa ORDER BY kelas_jurusan ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Manual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-4 md:p-8 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Absensi Manual</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?= date('d F Y', strtotime($tgl_pilih)) ?></p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-2 block">Cari Nama</label>
                        <input type="text" name="search" value="<?= htmlspecialchars($search_nama) ?>" placeholder="Nama siswa..." class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-2 block">Pilih Kelas</label>
                        <select name="kelas" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Kelas</option>
                            <?php while($k = mysqli_fetch_assoc($list_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>" <?= $kelas_pilih == $k['kelas_jurusan'] ? 'selected' : '' ?>><?= $k['kelas_jurusan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-2 block">Tanggal</label>
                        <input type="date" name="tgl" value="<?= $tgl_pilih ?>" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Cari</button>
                </form>
            </div>

            <?php if(isset($success)): ?>
                <div class="bg-emerald-100 text-emerald-600 p-4 rounded-2xl font-bold text-sm border border-emerald-200">
                    <i class="fas fa-check-circle mr-2"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $st = $row['status'] ?? 'belum absen';
                        $status_styles = [
                            'hadir' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                            'sakit' => 'bg-amber-100 text-amber-600 border-amber-200',
                            'izin'  => 'bg-indigo-100 text-indigo-600 border-indigo-200',
                            'alpha' => 'bg-rose-100 text-rose-600 border-rose-200',
                            'belum absen' => 'bg-slate-100 text-slate-400 border-slate-200'
                        ];
                    ?>
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 font-black uppercase text-xl">
                                <?= substr($row['nama_lengkap'], 0, 1) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-black text-slate-800 uppercase truncate leading-tight"><?= $row['nama_lengkap'] ?></h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"><?= $row['nis'] ?></p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border <?= $status_styles[$st] ?>">
                                <?= $st ?>
                            </span>
                        </div>

                        <form action="" method="POST" class="space-y-4">
                            <input type="hidden" name="sid" value="<?= $row['id'] ?>">
                            <input type="hidden" name="tgl" value="<?= $tgl_pilih ?>">
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1 block">Status</label>
                                    <select name="status" class="w-full px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl font-bold text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="hadir" <?= $st == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="sakit" <?= $st == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                        <option value="izin" <?= $st == 'izin' ? 'selected' : '' ?>>Izin</option>
                                        <option value="alpha" <?= $st == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1 block">Keterangan</label>
                                    <input type="text" name="ket" value="<?= $row['keterangan'] ?>" placeholder="..." class="w-full px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl font-bold text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                            
                            <button type="submit" name="update" class="w-full bg-indigo-600 text-white py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all">
                                Simpan
                            </button>
                        </form>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full py-20 text-center">
                        <i class="fas fa-search text-4xl text-slate-200 mb-4"></i>
                        <p class="text-slate-400 font-bold uppercase tracking-widest">Siswa tidak ditemukan</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
        
        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>