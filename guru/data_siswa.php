<?php
include '../include/config.php';

// Proteksi Halaman: Hanya Guru yang boleh masuk
if ($_SESSION['role'] != 'guru') {
    header("Location: ../login.php");
    exit;
}

// Tambah Siswa
if (isset($_POST['tambah_siswa'])) {
    $nis = escape($_POST['nis']);
    $nama = escape($_POST['nama_lengkap']);
    $kelas = escape($_POST['kelas_jurusan']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query_user = "INSERT INTO users (username, password, role) VALUES ('$nis', '$password', 'siswa')";
    if (mysqli_query($conn, $query_user)) {
        $user_id = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO siswa (user_id, nis, nama_lengkap, kelas_jurusan) VALUES ('$user_id', '$nis', '$nama', '$kelas')");
        $success = "Siswa berhasil didaftarkan!";
    }
}

// Hapus Siswa
if (isset($_GET['hapus'])) {
    $id_user = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user'");
    header("Location: data_siswa.php");
}

$result_siswa = mysqli_query($conn, "SELECT siswa.*, users.id as user_id FROM siswa JOIN users ON siswa.user_id = users.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa - Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">
    <?php include '../include/sidebar.php'; ?>
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include '../include/header_nav.php'; ?>
        
        <main class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Data Siswa</h1>
                <button onclick="document.getElementById('modalSiswa').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-md">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Siswa
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
                        <tr>
                            <th class="px-6 py-4">NIS</th>
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4">Kelas/Jurusan</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php while($s = mysqli_fetch_assoc($result_siswa)): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono"><?= $s['nis'] ?></td>
                            <td class="px-6 py-4 font-medium"><?= $s['nama_lengkap'] ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= $s['kelas_jurusan'] ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="?hapus=<?= $s['user_id'] ?>" onclick="return confirm('Hapus siswa ini?')" class="text-red-500 hover:text-red-700 mx-2">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <div id="modalSiswa" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden p-4">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
                <div class="bg-indigo-600 p-4 text-white flex justify-between font-bold">
                    <span>Tambah Siswa</span>
                    <button onclick="document.getElementById('modalSiswa').classList.add('hidden')"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-6 space-y-4">
                    <input type="text" name="nis" placeholder="NIS (Untuk Login)" required class="w-full px-4 py-2 border rounded-lg outline-none">
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required class="w-full px-4 py-2 border rounded-lg outline-none">
                    <input type="text" name="kelas_jurusan" placeholder="Kelas/Jurusan (Contoh: XII RPL 1)" required class="w-full px-4 py-2 border rounded-lg outline-none">
                    <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg outline-none">
                    <button type="submit" name="tambah_siswa" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold">Simpan Siswa</button>
                </form>
            </div>
        </div>

        <?php include '../include/footer.php'; ?>
    </div>
</body>
</html>