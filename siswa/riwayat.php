<?php
include '../include/config.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') { 
    header("Location: ../login.php"); 
    exit; 
}

$user_id = $_SESSION['id'];
$siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$user_id'"));

if (!$siswa) {
    echo "Data tidak ditemukan.";
    exit;
}

$siswa_id = $siswa['id'];
$query_riwayat = "SELECT * FROM absensi WHERE siswa_id = '$siswa_id' ORDER BY tanggal DESC LIMIT 30";
$result_riwayat = mysqli_query($conn, $query_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi - R-ABSEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        <main class="p-4 md:p-8">
            <h1 class="text-2xl font-black text-slate-800 mb-8 uppercase tracking-tight">Riwayat 30 Hari Terakhir</h1>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em] border-b border-slate-100">
                                <th class="px-8 py-6">Tanggal</th>
                                <th class="px-8 py-6">Jam Scan</th>
                                <th class="px-8 py-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if(mysqli_num_rows($result_riwayat) > 0): while($row = mysqli_fetch_assoc($result_riwayat)): 
                                $colors = ['hadir'=>'bg-emerald-50 text-emerald-600', 'sakit'=>'bg-amber-50 text-amber-600', 'izin'=>'bg-blue-50 text-blue-600', 'alpha'=>'bg-rose-50 text-rose-600'];
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6 font-bold text-slate-700 text-sm"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td class="px-8 py-6 font-black text-slate-800 tracking-tighter"><?= $row['jam_masuk'] != '00:00:00' ? $row['jam_masuk'] : '--:--' ?></td>
                                <td class="px-8 py-6">
                                    <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border <?= $colors[$row['status']] ?>"><?= $row['status'] ?></span>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="3" class="px-8 py-20 text-center font-bold text-slate-300 uppercase tracking-widest text-xs">Belum ada riwayat</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <?php include '../include/footer.php';?>
    </div>
</body>
</html>