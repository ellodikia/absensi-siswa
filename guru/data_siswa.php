<?php
include '../include/config.php';

// Proteksi Halaman: Hanya Guru yang boleh masuk
if ($_SESSION['role'] != 'guru') {
    header("Location: ../login.php");
    exit;
}

// ==========================================
// 1. PROSES TAMBAH SISWA
// ==========================================
if (isset($_POST['tambah_siswa'])) {
    $nis = escape($_POST['nis']);
    $nama = escape($_POST['nama_lengkap']);
    $kelas = escape($_POST['kelas_jurusan']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah NIS sudah ada
    $cek_nis = mysqli_query($conn, "SELECT id FROM users WHERE username='$nis'");
    if(mysqli_num_rows($cek_nis) > 0) {
        $error = "Gagal! NIS sudah terdaftar di sistem.";
    } else {
        $query_user = "INSERT INTO users (username, password, role) VALUES ('$nis', '$password', 'siswa')";
        if (mysqli_query($conn, $query_user)) {
            $user_id = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO siswa (user_id, nis, nama_lengkap, kelas_jurusan) VALUES ('$user_id', '$nis', '$nama', '$kelas')");
            $success = "Siswa berhasil didaftarkan!";
        }
    }
}

// ==========================================
// 2. PROSES EDIT SISWA (FITUR BARU)
// ==========================================
if (isset($_POST['edit_siswa'])) {
    $id_siswa = (int)$_POST['id_siswa'];
    $id_user = (int)$_POST['id_user'];
    $nis = escape($_POST['nis']);
    $nama = escape($_POST['nama_lengkap']);
    $kelas = escape($_POST['kelas_jurusan']);

    // Update tabel siswa
    mysqli_query($conn, "UPDATE siswa SET nis='$nis', nama_lengkap='$nama', kelas_jurusan='$kelas' WHERE id='$id_siswa'");
    
    // Update username (NIS) di tabel users
    mysqli_query($conn, "UPDATE users SET username='$nis' WHERE id='$id_user'");

    // Jika guru mengisi password baru, update passwordnya
    if (!empty($_POST['password'])) {
        $password_baru = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$password_baru' WHERE id='$id_user'");
    }

    $success = "Data siswa berhasil diperbarui!";
}

// ==========================================
// 3. PROSES HAPUS SISWA
// ==========================================
if (isset($_GET['hapus'])) {
    $id_user = (int)$_GET['hapus']; // Casting ke integer demi keamanan
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user'");
    header("Location: data_siswa.php");
    exit;
}

// Ambil semua data siswa
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
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-md transition">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Siswa
                </button>
            </div>

            <?php if(isset($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow-sm"><i class="fas fa-check-circle mr-2"></i><?= $success ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow-sm"><i class="fas fa-exclamation-circle mr-2"></i><?= $error ?></div>
            <?php endif; ?>

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
                            <td class="px-6 py-4 text-center flex justify-center space-x-3">
                                <button onclick="bukaModalEdit(<?= $s['id'] ?>, <?= $s['user_id'] ?>, '<?= $s['nis'] ?>', '<?= addslashes($s['nama_lengkap']) ?>', '<?= addslashes($s['kelas_jurusan']) ?>')" class="text-blue-500 hover:text-blue-700 transition" title="Edit Siswa">
                                    <i class="fas fa-edit text-lg"></i>
                                </button>
                                
                                <a href="?hapus=<?= $s['user_id'] ?>" onclick="return confirm('Hapus siswa <?= $s['nama_lengkap'] ?>?')" class="text-red-500 hover:text-red-700 transition" title="Hapus Siswa">
                                    <i class="fas fa-trash text-lg"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden p-4">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all">
                <div class="bg-indigo-600 p-4 text-white flex justify-between items-center font-bold">
                    <span><i class="fas fa-user-plus mr-2"></i> Tambah Siswa Baru</span>
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="hover:text-gray-300 text-xl"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">NIS (Untuk Login)</label>
                        <input type="text" name="nis" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas/Jurusan (Contoh: XII RPL 1)</label>
                        <input type="text" name="kelas_jurusan" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password Awal</label>
                        <input type="password" name="password" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="pt-2">
                        <button type="submit" name="tambah_siswa" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition">Simpan Siswa</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden p-4">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all">
                <div class="bg-blue-600 p-4 text-white flex justify-between items-center font-bold">
                    <span><i class="fas fa-edit mr-2"></i> Edit Data Siswa</span>
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="hover:text-gray-300 text-xl"><i class="fas fa-times"></i></button>
                </div>
                <form action="" method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="id_siswa" id="edit_id_siswa">
                    <input type="hidden" name="id_user" id="edit_id_user">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">NIS (Untuk Login)</label>
                        <input type="text" name="nis" id="edit_nis" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="edit_nama" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas/Jurusan</label>
                        <input type="text" name="kelas_jurusan" id="edit_kelas" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <label class="block text-sm font-semibold text-blue-800 mb-1">Reset Password Baru?</label>
                        <p class="text-xs text-blue-600 mb-2">Kosongkan jika tidak ingin mengubah password.</p>
                        <input type="password" name="password" placeholder="Ketik password baru disini..." class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="pt-2">
                        <button type="submit" name="edit_siswa" class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-bold hover:bg-blue-700 transition">Update Data Siswa</button>
                    </div>
                </form>
            </div>
        </div>

        <?php include '../include/footer.php'; ?>
    </div>

    <script>
        function bukaModalEdit(id_siswa, id_user, nis, nama, kelas) {
            // Masukkan data ke dalam form modal
            document.getElementById('edit_id_siswa').value = id_siswa;
            document.getElementById('edit_id_user').value = id_user;
            document.getElementById('edit_nis').value = nis;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_kelas').value = kelas;
            
            // Tampilkan modal
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>
</body>
</html>