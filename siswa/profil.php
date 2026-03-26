<?php
include '../include/config.php'; 

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') { 
    header("Location: ../login.php"); 
    exit; 
}

$user_id = $_SESSION['id'];
$pesan = "";

if (isset($_POST['update_profil'])) {
    $username_baru = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $password_baru = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    $error = false;

    mysqli_query($conn, "UPDATE siswa SET nama_lengkap = '$nama_lengkap', nis = '$username_baru' WHERE user_id = '$user_id'");

    mysqli_query($conn, "UPDATE users SET username = '$username_baru' WHERE id = '$user_id'");

    if (!empty($password_baru)) {
        if ($password_baru !== $konfirmasi_password) {
            $pesan = "<div class='bg-rose-100 text-rose-600 p-4 rounded-2xl mb-6 font-bold text-sm border-rose-200'>Password tidak cocok!</div>";
            $error = true;
        } else {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'");
        }
    }

    if (!$error) {
        $pesan = "<div class='bg-emerald-100 text-emerald-600 p-4 rounded-2xl mb-6 font-bold text-sm border-emerald-200'>Profil diperbarui!</div>";
    }
}

$query_user = mysqli_query($conn, "SELECT users.username, siswa.* FROM users JOIN siswa ON users.id = siswa.user_id WHERE users.id = '$user_id'");
$data_siswa = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="../img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - R-ABSEN</title>
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
                        <i class="fas fa-user-graduate text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Profil Siswa</h1>
                </div>

                <?= $pesan ?>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <form action="" method="POST" class="p-8 md:p-10 space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">NIS / Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($data_siswa['username']) ?>" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data_siswa['nama_lengkap']) ?>" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-sm">
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl space-y-4 border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Ganti Password</p>
                            <input type="password" name="password" placeholder="Password Baru" class="w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl font-bold text-sm">
                            <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password" class="w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl font-bold text-sm">
                        </div>

                        <button type="submit" name="update_profil" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest text-xs shadow-lg">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </main>
        <?php include '../include/footer.php';?>
    </div>
</body>
</html>