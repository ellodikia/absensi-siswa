<?php
include '../include/config.php'; 

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'guru') { 
    header("Location: ../login.php"); 
    exit; 
}

$user_id = $_SESSION['id'];
$pesan = "";

if (isset($_POST['update_profil'])) {
    $username_baru = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_guru = mysqli_real_escape_string($conn, $_POST['nama_guru']);
    $mapel = mysqli_real_escape_string($conn, $_POST['mapel']);
    
    $walikelas_di = mysqli_real_escape_string($conn, $_POST['walikelas_di']);
    $is_walikelas = (!empty($walikelas_di)) ? 1 : 0;

    $error = false;

  
    $update_guru = mysqli_query($conn, "UPDATE guru SET 
        nama_guru = '$nama_guru', 
        mapel = '$mapel', 
        is_walikelas = '$is_walikelas' 
        WHERE user_id = '$user_id'");

    $update_user = mysqli_query($conn, "UPDATE users SET username = '$username_baru' WHERE id = '$user_id'");

    if (!empty($password_baru)) {
        if ($_POST['password'] !== $_POST['konfirmasi_password']) {
            $pesan = "<div class='bg-rose-100 text-rose-600 p-4 rounded-2xl mb-6 font-bold text-sm border border-rose-200'>Konfirmasi password tidak cocok!</div>";
            $error = true;
        } else {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'");
        }
    }

    if (!$error) {
        $_SESSION['username'] = $username_baru; 
        $pesan = "<div class='bg-emerald-100 text-emerald-600 p-4 rounded-2xl mb-6 font-bold text-sm border border-emerald-200'>Profil berhasil diperbarui!</div>";
    }
}

$query_kelas = mysqli_query($conn, "SELECT DISTINCT kelas_jurusan FROM siswa WHERE kelas_jurusan != ''");

$query_user = mysqli_query($conn, "SELECT users.username, guru.* FROM users 
                                   JOIN guru ON users.id = guru.user_id 
                                   WHERE users.id = '$user_id'");
$data_user = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">
    <?php include '../include/sidebar.php'; ?>
    
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-4 md:p-8 flex justify-center">
            <div class="w-full max-w-2xl">
                <div class="flex items-center gap-4 mb-8">
                    <div class="bg-indigo-600 w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-user-edit text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Pengaturan Profil</h1>
                    </div>
                </div>

                <?= $pesan ?>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <form action="" method="POST" class="p-8 md:p-10 space-y-5">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Username / NIP</label>
                                <input type="text" name="username" value="<?= htmlspecialchars($data_user['username']) ?>" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Nama Lengkap</label>
                                <input type="text" name="nama_guru" value="<?= htmlspecialchars($data_user['nama_guru']) ?>" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Mata Pelajaran</label>
                            <input type="text" name="mapel" value="<?= htmlspecialchars($data_user['mapel']) ?>" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Wali Kelas Di (Opsional)</label>
                            <select name="walikelas_di" class="w-full px-6 py-4 bg-indigo-50 border border-indigo-100 rounded-2xl font-bold text-sm text-indigo-700 outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Bukan Wali Kelas / Kosongkan --</option>
                                <?php while($row_kelas = mysqli_fetch_assoc($query_kelas)): ?>
                                    <option value="<?= $row_kelas['kelas_jurusan'] ?>" <?= ($data_user['is_walikelas'] == 1) ? 'selected' : '' ?>>
                                        Wali Kelas <?= htmlspecialchars($row_kelas['kelas_jurusan']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl space-y-4 border border-slate-100 mt-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Ganti Password</p>
                            <input type="password" name="password" placeholder="Password Baru" class="w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl font-bold text-sm">
                            <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password" class="w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl font-bold text-sm">
                        </div>

                        <button type="submit" name="update_profil" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-indigo-700 transition-all shadow-lg">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>