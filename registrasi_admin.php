<?php
include 'include/config.php';



$pesan = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $role = 'admin'; 
    if ($password !== $konfirmasi_password) {
        $pesan = "<div class='bg-rose-100 text-rose-600 p-4 rounded-xl mb-4'>Password tidak cocok!</div>";
    } else {
        $cek_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            $pesan = "<div class='bg-amber-100 text-amber-600 p-4 rounded-xl mb-4'>Username sudah terdaftar!</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
            
            if (mysqli_query($conn, $query)) {
                $pesan = "<div class='bg-emerald-100 text-emerald-600 p-4 rounded-xl mb-4'>Admin berhasil ditambahkan!</div>";
            } else {
                $pesan = "<div class='bg-rose-100 text-rose-600 p-4 rounded-xl mb-4'>Gagal mendaftar: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100 w-full max-w-md">
        <h1 class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight">Tambah Admin</h1>
        <p class="text-slate-400 text-sm mb-6">Daftarkan akun administrator baru ke sistem.</p>

        <?= $pesan ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Username</label>
                <input type="text" name="username" required class="w-full px-6 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Password</label>
                <input type="password" name="password" required class="w-full px-6 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-2">Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" required class="w-full px-6 py-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" name="register" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 mt-4">
                Daftarkan Admin
            </button>
            <a href="dashboard.php" class="block text-center text-slate-400 text-[10px] font-black uppercase tracking-widest mt-4 hover:text-indigo-600 transition-colors">Kembali ke Dashboard</a>
        </form>
    </div>
</body>
</html>