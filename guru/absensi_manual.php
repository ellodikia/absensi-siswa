<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

$tgl_pilih = $_GET['tgl'] ?? date('Y-m-d');
$kelas_pilih = $_GET['kelas'] ?? '';

if (isset($_POST['update'])) {
    $sid = $_POST['sid']; $st = $_POST['status']; $t = $_POST['tgl']; $ket = escape($_POST['ket']);
    $jam = ($st == 'hadir') ? date('H:i:s') : '00:00:00';
    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id='$sid' AND tanggal='$t'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE absensi SET status='$st', keterangan='$ket' WHERE siswa_id='$sid' AND tanggal='$t'");
    } else {
        mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, status, keterangan, jam_masuk) VALUES ('$sid', '$t', '$st', '$ket', '$jam')");
    }
    $success = "Data berhasil diperbarui!";
}

$query_tampil = "SELECT s.id, s.nama_lengkap, s.kelas_jurusan, s.nis, a.status, a.keterangan FROM siswa s LEFT JOIN absensi a ON s.id = a.siswa_id AND a.tanggal = '$tgl_pilih'";
if ($kelas_pilih) $query_tampil .= " WHERE s.kelas_jurusan = '$kelas_pilih'";
$result = mysqli_query($conn, $query_tampil . " ORDER BY s.nama_lengkap ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
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
        <main class="p-4 md:p-8">
            <h1 class="text-2xl font-black text-slate-800 mb-8 uppercase tracking-tight">Absensi Manual</h1>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8 no-print">
                <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-2 block">Pilih Tanggal</label>
                        <input type="date" name="tgl" value="<?= $tgl_pilih ?>" class="w-full px-6 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Filter</button>
                </form>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em] border-b border-slate-100">
                                <th class="px-8 py-6">Siswa</th>
                                <th class="px-8 py-6">Status Saat Ini</th>
                                <th class="px-8 py-6">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($row = mysqli_fetch_assoc($result)): 
                                $st = $row['status'] ?? 'belum absen';
                                $color = ['hadir'=>'bg-emerald-50 text-emerald-600','sakit'=>'bg-amber-50 text-amber-600','izin'=>'bg-blue-50 text-blue-600','alpha'=>'bg-rose-50 text-rose-600'];
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-800 uppercase tracking-tight"><?= $row['nama_lengkap'] ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?= $row['nis'] ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border <?= $color[$st] ?? 'bg-slate-50 text-slate-400 border-slate-100' ?>"><?= $st ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <form action="" method="POST" class="flex flex-wrap gap-2">
                                        <input type="hidden" name="sid" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="tgl" value="<?= $tgl_pilih ?>">
                                        <select name="status" class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl font-bold text-xs outline-none">
                                            <option value="hadir" <?= $st == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                            <option value="sakit" <?= $st == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                            <option value="izin" <?= $st == 'izin' ? 'selected' : '' ?>>Izin</option>
                                            <option value="alpha" <?= $st == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                                        </select>
                                        <input type="text" name="ket" value="<?= $row['keterangan'] ?>" placeholder="Ket" class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl font-bold text-xs outline-none w-24">
                                        <button type="submit" name="update" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md">Simpan</button>
                                    </form>
                                </td>
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