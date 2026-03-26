<?php
include '../include/config.php';
if ($_SESSION['role'] != 'guru') { header("Location: ../login.php"); exit; }

if (isset($_POST['tambah_siswa'])) {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas_jurusan']);
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

if (isset($_POST['edit_siswa'])) {
    $id_siswa = (int)$_POST['id_siswa'];
    $user_id = (int)$_POST['user_id'];
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas_jurusan']);

    $update_siswa = "UPDATE siswa SET nis='$nis', nama_lengkap='$nama', kelas_jurusan='$kelas' WHERE id='$id_siswa'";
    $update_user = "UPDATE users SET username='$nis' WHERE id='$user_id'";

    if (mysqli_query($conn, $update_siswa) && mysqli_query($conn, $update_user)) {
        if (!empty($_POST['password'])) {
            $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$new_pass' WHERE id='$user_id'");
        }
        $success = "Data siswa berhasil diperbarui!";
    }
}

if (isset($_GET['hapus'])) {
    $id_user = (int)$_GET['hapus'];
    $get_siswa = mysqli_query($conn, "SELECT id FROM siswa WHERE user_id = '$id_user'");
    $data_siswa = mysqli_fetch_assoc($get_siswa);

    if ($data_siswa) {
        $siswa_id = $data_siswa['id'];
        mysqli_query($conn, "DELETE FROM absensi WHERE siswa_id = '$siswa_id'");
        mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user'");
    }
    header("Location: data_siswa.php");
    exit;
}

$filter_kelas = isset($_GET['filter_kelas']) ? mysqli_real_escape_string($conn, $_GET['filter_kelas']) : '';
$search_nama = isset($_GET['search_nama']) ? mysqli_real_escape_string($conn, $_GET['search_nama']) : '';

$query_sql = "SELECT siswa.*, users.id as user_id FROM siswa JOIN users ON siswa.user_id = users.id WHERE 1=1";
if ($filter_kelas != '') $query_sql .= " AND siswa.kelas_jurusan = '$filter_kelas'";
if ($search_nama != '') $query_sql .= " AND siswa.nama_lengkap LIKE '%$search_nama%'";
$query_sql .= " ORDER BY siswa.nama_lengkap ASC";

$result_siswa = mysqli_query($conn, $query_sql);
$list_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa ORDER BY kelas_jurusan ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Siswa</title>
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
                    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Manajemen Siswa</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total: <?= mysqli_num_rows($result_siswa) ?> Siswa</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="w-full md:w-auto bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Tambah Siswa
                </button>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-2 block">Cari Nama</label>
                        <input type="text" name="search_nama" value="<?= htmlspecialchars($search_nama) ?>" placeholder="Masukkan nama..." class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-2 block">Saring Kelas</label>
                        <select name="filter_kelas" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Kelas</option>
                            <?php while($k = mysqli_fetch_assoc($list_kelas)): ?>
                                <option value="<?= $k['kelas_jurusan'] ?>" <?= $filter_kelas == $k['kelas_jurusan'] ? 'selected' : '' ?>><?= $k['kelas_jurusan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all">Cari</button>
                        <?php if($filter_kelas != '' || $search_nama != ''): ?>
                            <a href="data_siswa.php" class="bg-rose-50 text-rose-500 px-4 py-3 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-sync-alt"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if(isset($success)): ?>
                <div class="bg-emerald-100 text-emerald-600 p-4 rounded-2xl font-bold text-sm border border-emerald-200"><?= $success ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <?php if(mysqli_num_rows($result_siswa) > 0): ?>
                    <?php while($s = mysqli_fetch_assoc($result_siswa)): ?>
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group relative">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 font-black uppercase text-xl">
                                <?= substr($s['nama_lengkap'], 0, 1) ?>
                            </div>
                            <div class="flex gap-1">
                                <button onclick='openEditModal(<?= json_encode($s) ?>)' class="w-8 h-8 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all"><i class="fas fa-edit text-xs"></i></button>
                                <a href="?hapus=<?= $s['user_id'] ?>" onclick="return confirm('Hapus data?')" class="w-8 h-8 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash-alt text-xs"></i></a>
                            </div>
                        </div>
                        <p class="text-[9px] font-black text-indigo-500 tracking-widest uppercase"><?= $s['nis'] ?></p>
                        <h3 class="text-sm font-black text-slate-800 truncate uppercase leading-tight mb-1"><?= $s['nama_lengkap'] ?></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= $s['kelas_jurusan'] ?></p>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full py-20 text-center text-slate-300 font-black uppercase tracking-widest text-xs">Siswa tidak ditemukan</div>
                <?php endif; ?>
            </div>
        </main>

        <div id="modalTambah" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="bg-indigo-600 p-6 text-white flex justify-between items-center">
                    <h3 class="font-black uppercase tracking-widest text-xs">Tambah Siswa</h3>
                    <button onclick="document.getElementById('modalTambah').classList.add('hidden')"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-8 space-y-4">
                    <input type="text" name="nis" placeholder="NISN" required class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="kelas_jurusan" placeholder="Kelas (Contoh: XI RPL)" required class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="password" name="password" placeholder="Password Akun" required class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" name="tambah_siswa" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-black uppercase tracking-widest text-xs shadow-lg">Simpan</button>
                </form>
            </div>
        </div>

        <div id="modalEdit" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="bg-amber-500 p-6 text-white flex justify-between items-center">
                    <h3 class="font-black uppercase tracking-widest text-xs">Edit Siswa</h3>
                    <button onclick="document.getElementById('modalEdit').classList.add('hidden')"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-8 space-y-4">
                    <input type="hidden" name="id_siswa" id="edit_id_siswa">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <input type="text" name="nis" id="edit_nis" required class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none">
                    <input type="text" name="nama_lengkap" id="edit_nama" required class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none">
                    <input type="text" name="kelas_jurusan" id="edit_kelas" required class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none">
                    <input type="password" name="password" placeholder="Password Baru (Opsional)" class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none">
                    <button type="submit" name="edit_siswa" class="w-full bg-amber-500 text-white py-4 rounded-xl font-black uppercase tracking-widest text-xs shadow-lg">Perbarui</button>
                </form>
            </div>
        </div>
        <?php include '../include/footer.php'; ?>
    </div>
    <script>
        function openEditModal(data) {
            document.getElementById('edit_id_siswa').value = data.id;
            document.getElementById('edit_user_id').value = data.user_id;
            document.getElementById('edit_nis').value = data.nis;
            document.getElementById('edit_nama').value = data.nama_lengkap;
            document.getElementById('edit_kelas').value = data.kelas_jurusan;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>
</body>
</html>