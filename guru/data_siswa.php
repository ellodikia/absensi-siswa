<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

if (isset($_POST['tambah_siswa'])) {
    $nis = escape($_POST['nis']);
    $nama = escape($_POST['nama_lengkap']);
    $kelas = escape($_POST['kelas_jurusan']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek_nis = mysqli_query($conn, "SELECT id FROM users WHERE username='$nis'");
    if(mysqli_num_rows($cek_nis) > 0) {
        $error = "NIS sudah terdaftar!";
    } else {
        $query_user = "INSERT INTO users (username, password, role) VALUES ('$nis', '$password', 'siswa')";
        if (mysqli_query($conn, $query_user)) {
            $user_id = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO siswa (user_id, nis, nama_lengkap, kelas_jurusan) VALUES ('$user_id', '$nis', '$nama', '$kelas')");
            $success = "Siswa berhasil didaftarkan!";
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_user = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user'");
    header("Location: data_siswa.php");
    exit;
}

$result_siswa = mysqli_query($conn, "SELECT siswa.*, users.id as user_id FROM siswa JOIN users ON siswa.user_id = users.id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        <main class="p-4 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Data Siswa</h1>
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="w-full sm:w-auto bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">
                    <i class="fas fa-plus mr-2"></i> TAMBAH SISWA
                </button>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em] border-b border-slate-100">
                                <th class="px-8 py-6">NIS / Nama Siswa</th>
                                <th class="px-8 py-6">Kelas & Jurusan</th>
                                <th class="px-8 py-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($s = mysqli_fetch_assoc($result_siswa)): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-800 uppercase tracking-tight"><?= $s['nama_lengkap'] ?></p>
                                    <p class="text-xs font-bold text-slate-400 mt-1"><?= $s['nis'] ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-bold bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl uppercase border border-indigo-100"><?= $s['kelas_jurusan'] ?></span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <a href="?hapus=<?= $s['user_id'] ?>" onclick="return confirm('Hapus siswa ini?')" class="w-10 h-10 inline-flex items-center justify-center bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <div id="modalTambah" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center hidden p-4">
            <div class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
                <div class="bg-indigo-600 p-8 text-white flex justify-between items-center">
                    <h3 class="font-black uppercase tracking-widest text-sm">Tambah Siswa</h3>
                    <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-xl hover:bg-white/20 transition-all"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-8 space-y-5">
                    <input type="text" name="nis" placeholder="NISN (Username Login)" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap Siswa" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <input type="text" name="kelas_jurusan" placeholder="Contoh: XII RPL 1" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <input type="password" name="password" placeholder="Password Akun Siswa" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <button type="submit" name="tambah_siswa" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Daftarkan Siswa</button>
                </form>
            </div>
        </div>
        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>