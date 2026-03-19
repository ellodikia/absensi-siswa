<?php
include '../include/config.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

if (isset($_POST['tambah_guru'])) {
    $nip = escape($_POST['nip']);
    $nama = escape($_POST['nama_guru']);
    $mapel = escape($_POST['mapel']);
    $is_walikelas = isset($_POST['is_walikelas']) ? 1 : 0;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query_user = "INSERT INTO users (username, password, role) VALUES ('$nip', '$password', 'guru')";
    if (mysqli_query($conn, $query_user)) {
        $user_id = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO guru (user_id, nama_guru, mapel, is_walikelas) VALUES ('$user_id', '$nama', '$mapel', '$is_walikelas')");
        $success = "Data guru berhasil disimpan!";
    } else {
        $error = "Gagal! NIP mungkin sudah terdaftar.";
    }
}

if (isset($_GET['hapus'])) {
    $id_user = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user'");
    header("Location: data_guru.php");
    exit;
}

$result_guru = mysqli_query($conn, "SELECT guru.*, users.username, users.id as user_id FROM guru JOIN users ON guru.user_id = users.id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        <main class="p-4 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Data Guru</h1>
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="w-full sm:w-auto bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">
                    <i class="fas fa-plus mr-2"></i> TAMBAH GURU
                </button>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em] border-b border-slate-100">
                                <th class="px-8 py-6">NIP / Nama</th>
                                <th class="px-8 py-6">Mata Pelajaran</th>
                                <th class="px-8 py-6">Wali Kelas</th>
                                <th class="px-8 py-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($row = mysqli_fetch_assoc($result_guru)): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-800 uppercase tracking-tight"><?= $row['nama_guru'] ?></p>
                                    <p class="text-xs font-bold text-slate-400 mt-1"><?= $row['username'] ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-lg uppercase"><?= $row['mapel'] ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center <?= $row['is_walikelas'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-300' ?>">
                                        <i class="fas <?= $row['is_walikelas'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <a href="?hapus=<?= $row['user_id'] ?>" onclick="return confirm('Hapus guru ini?')" class="w-10 h-10 inline-flex items-center justify-center bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
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
                    <h3 class="font-black uppercase tracking-widest text-sm">Tambah Guru</h3>
                    <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-xl hover:bg-white/20 transition-all"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-8 space-y-5">
                    <input type="text" name="nip" placeholder="NIP (Username)" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <input type="text" name="nama_guru" placeholder="Nama Lengkap" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <input type="text" name="mapel" placeholder="Mata Pelajaran" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <input type="password" name="password" placeholder="Password" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-sm">
                    <label class="flex items-center gap-3 px-2 cursor-pointer group">
                        <input type="checkbox" name="is_walikelas" class="w-5 h-5 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-bold text-slate-500 group-hover:text-slate-800 transition-colors">Set sebagai Wali Kelas</span>
                    </label>
                    <button type="submit" name="tambah_guru" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Simpan Data</button>
                </form>
            </div>
        </div>
        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>