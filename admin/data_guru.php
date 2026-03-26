<?php
include '../include/config.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

if (isset($_POST['simpan_guru'])) {
    $nip = escape($_POST['nip']);
    $nama = escape($_POST['nama_guru']);
    $mapel = escape($_POST['mapel']);
    $is_walikelas = escape($_POST['is_walikelas']);
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

    if ($user_id > 0) {
        mysqli_query($conn, "UPDATE users SET username = '$nip' WHERE id = '$user_id'");
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password = '$pass' WHERE id = '$user_id'");
        }
        mysqli_query($conn, "UPDATE guru SET nama_guru = '$nama', mapel = '$mapel', is_walikelas = '$is_walikelas' WHERE user_id = '$user_id'");
        $success = "Data guru berhasil diperbarui!";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query_user = "INSERT INTO users (username, password, role) VALUES ('$nip', '$password', 'guru')";
        if (mysqli_query($conn, $query_user)) {
            $new_id = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO guru (user_id, nama_guru, mapel, is_walikelas) VALUES ('$new_id', '$nama', '$mapel', '$is_walikelas')");
            $success = "Data guru berhasil disimpan!";
        } else {
            $error = "Gagal! NIP mungkin sudah terdaftar.";
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_user = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user'");
    header("Location: data_guru.php"); exit;
}

$list_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa ORDER BY kelas_jurusan ASC");
$result_guru = mysqli_query($conn, "SELECT guru.*, users.username, users.id as user_id FROM guru JOIN users ON guru.user_id = users.id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex overflow-x-hidden">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen max-w-full overflow-hidden">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-4 md:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Manajemen Guru</h1>
                </div>
                <button onclick="bukaModal()" class="w-full sm:w-auto bg-indigo-600 text-white px-8 py-4 rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all active:scale-95">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Guru
                </button>
            </div>

            <?php if(isset($success)): ?> 
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 p-4 rounded-2xl font-bold flex items-center gap-3">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div> 
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                <?php while($row = mysqli_fetch_assoc($result_guru)): ?>
                <div class="bg-white rounded-[2.5rem] p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all group relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center group-hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-slate-100 group-hover:text-indigo-100 text-4xl"></i>
                    </div>

                    <div class="relative z-10 flex flex-col h-full">
                        <div class="mb-6">
                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-1"><?= $row['username'] ?></p>
                            <h2 class="text-lg font-black text-slate-800 uppercase leading-tight truncate"><?= $row['nama_guru'] ?></h2>
                        </div>

                        <div class="space-y-3 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-book text-xs"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider"><?= $row['mapel'] ?></span>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-star text-xs"></i>
                                </div>
                                <?php if (!empty($row['is_walikelas'])): ?>
                                    <span class="text-[10px] font-black bg-emerald-100 text-emerald-600 px-3 py-1 rounded-lg uppercase">Wali Kelas <?= $row['is_walikelas'] ?></span>
                                <?php else: ?>
                                    <span class="text-[10px] font-black bg-slate-100 text-slate-400 px-3 py-1 rounded-lg uppercase tracking-tighter">Bukan Wali Kelas</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-auto flex gap-2">
                            <button onclick='bukaModal(<?= json_encode($row) ?>)' class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </button>
                            <a href="?hapus=<?= $row['user_id'] ?>" onclick="return confirm('Hapus data guru ini?')" class="w-14 bg-rose-50 text-rose-500 flex items-center justify-center rounded-2xl hover:bg-rose-500 hover:text-white transition-all">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </main>

        <div id="modalGuru" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center hidden p-4">
            <div class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden shadow-2xl animate-in zoom-in duration-300">
                <div class="p-8 pb-0 flex justify-between items-center">
                    <h3 id="modalTitle" class="font-black uppercase tracking-[0.2em] text-xs text-slate-400">Tambah Guru</h3>
                    <button onclick="tutupModal()" class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-8 space-y-4">
                    <input type="hidden" name="user_id" id="form_user_id">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">NIP (Username)</label>
                        <input type="text" name="nip" id="form_nip" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Nama Lengkap</label>
                        <input type="text" name="nama_guru" id="form_nama" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Mata Pelajaran</label>
                        <input type="text" name="mapel" id="form_mapel" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Password</label>
                        <input type="password" name="password" id="form_pass" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Wali Kelas</label>
                        <select name="is_walikelas" id="form_wk" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm outline-none appearance-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">Bukan Wali Kelas</option>
                            <?php mysqli_data_seek($list_kelas, 0); while($k = mysqli_fetch_assoc($list_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>">Wali Kelas <?= $k['kelas_jurusan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" name="simpan_guru" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all">Simpan Data</button>
                </form>
            </div>
        </div>
        
        <?php include '../include/footer.php'; ?>
    </div>

    <script>
        function bukaModal(data = null) {
            const modal = document.getElementById('modalGuru');
            const title = document.getElementById('modalTitle');
            const passInput = document.getElementById('form_pass');
            
            if (data) {
                title.innerText = 'Edit Data Guru';
                document.getElementById('form_user_id').value = data.user_id;
                document.getElementById('form_nip').value = data.username;
                document.getElementById('form_nama').value = data.nama_guru;
                document.getElementById('form_mapel').value = data.mapel;
                document.getElementById('form_wk').value = data.is_walikelas;
                passInput.placeholder = "Isi hanya jika ingin ganti password";
                passInput.required = false; 
            } else {
                title.innerText = 'Tambah Guru Baru';
                document.getElementById('form_user_id').value = '';
                document.querySelector('form').reset();
                passInput.placeholder = "Password";
                passInput.required = true; 
            }
            modal.classList.remove('hidden');
        }

        function tutupModal() {
            document.getElementById('modalGuru').classList.add('hidden');
        }
    </script>
</body>
</html>