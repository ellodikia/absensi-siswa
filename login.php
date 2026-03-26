<?php
include 'include/config.php';

if (isset($_POST['login'])) {
    $username = escape($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id'] = $row['id']; 
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];

            if ($row['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($row['role'] == 'guru') {
                header("Location: guru/dashboard.php");
            } else {
                header("Location: siswa/dashboard.php");
            }
            exit;
        }
    }
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="img/logo.jpeg" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RAbsen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200">
        <div class="text-center mb-8">
            <div class="bg-indigo-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg rotate-3">
                <i class="fas fa-qrcode text-white text-4xl -rotate-3"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">R-ABSEN</h2>
            <p class="text-slate-500 font-medium">Silahkan masuk ke akun Anda</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm flex items-center border border-red-100">
                <i class="fas fa-exclamation-circle mr-2"></i> Username atau Password salah!
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Username / NISN / NIP</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="username" placeholder="Asep Budi/012131" required class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all font-medium">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="password" name="password" placeholder="********" required class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all font-medium">
                </div>
            </div>
            <button type="submit" name="login" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-[0.98]">
                MASUK SEKARANG <i class="fas fa-sign-in-alt ml-2"></i>
            </button>
        </form>
        
    </div>
</body>
</html>