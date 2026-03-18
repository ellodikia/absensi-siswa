<?php
include '../include/config.php';

// Proteksi Halaman: Hanya Admin yang boleh masuk
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Prosedur Tambah Guru
if (isset($_POST['tambah_guru'])) {
    $nip = escape($_POST['nip']); // Digunakan sebagai username
    $nama = escape($_POST['nama_guru']);
    $mapel = escape($_POST['mapel']);
    $is_walikelas = isset($_POST['is_walikelas']) ? 1 : 0;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 1. Insert ke tabel users dulu
    $query_user = "INSERT INTO users (username, password, role) VALUES ('$nip', '$password', 'guru')";
    if (mysqli_query($conn, $query_user)) {
        $user_id = mysqli_insert_id($conn);
        // 2. Insert ke tabel guru
        $query_guru = "INSERT INTO guru (user_id, nama_guru, mapel, is_walikelas) 
                       VALUES ('$user_id', '$nama', '$mapel', '$is_walikelas')";
        mysqli_query($conn, $query_guru);
        $success = "Guru berhasil ditambahkan!";
    } else {
        $error = "Gagal menambah guru (NIP mungkin sudah terdaftar).";
    }
}

// Prosedur Hapus Guru
if (isset($_GET['hapus'])) {
    $id_user = $_GET['hapus'];
    // Karena tabel guru menggunakan ON DELETE CASCADE, menghapus user otomatis menghapus data guru
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user'");
    header("Location: data_guru.php");
}

// Ambil Data Guru untuk ditampilkan
$query_tampil = "SELECT guru.*, users.username, users.id as user_id 
                 FROM guru 
                 JOIN users ON guru.user_id = users.id";
$result_guru = mysqli_query($conn, $query_tampil);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Guru - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">

    <?php include '../include/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>

        <main class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Daftar Guru Pengajar</h1>
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-md">
                    <i class="fas fa-plus mr-2"></i> Tambah Guru
                </button>
            </div>

            <?php if(isset($success)): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4 border-l-4 border-green-500"><?= $success ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                                <th class="px-6 py-4 font-semibold">NIP (Username)</th>
                                <th class="px-6 py-4 font-semibold">Nama Lengkap</th>
                                <th class="px-6 py-4 font-semibold">Mata Pelajaran</th>
                                <th class="px-6 py-4 font-semibold">Wali Kelas</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php while($row = mysqli_fetch_assoc($result_guru)): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-700 font-mono"><?= $row['username'] ?></td>
                                <td class="px-6 py-4 text-gray-800 font-medium"><?= $row['nama_guru'] ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= $row['mapel'] ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold <?= $row['is_walikelas'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' ?>">
                                        <?= $row['is_walikelas'] ? 'YA' : 'TIDAK' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="?hapus=<?= $row['user_id'] ?>" onclick="return confirm('Hapus guru ini? Akun login juga akan terhapus.')" class="text-red-500 hover:text-red-700 transition mx-2">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden p-4">
            <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="bg-indigo-600 p-4 text-white flex justify-between">
                    <h3 class="font-bold">Tambah Guru Baru</h3>
                    <button onclick="document.getElementById('modalTambah').classList.add('hidden')"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIP (Untuk Login)</label>
                        <input type="text" name="nip" required class="w-full mt-1 px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_guru" required class="w-full mt-1 px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                        <input type="text" name="mapel" required class="w-full mt-1 px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required class="w-full mt-1 px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="is_walikelas" id="wk" class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                        <label for="wk" class="text-sm text-gray-700">Set sebagai Wali Kelas</label>
                    </div>
                    <div class="pt-4">
                        <button type="submit" name="tambah_guru" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold hover:bg-indigo-700">Simpan Data Guru</button>
                    </div>
                </form>
            </div>
        </div>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>