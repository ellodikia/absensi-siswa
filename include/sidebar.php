<?php $role = $_SESSION['role']; ?>
<aside id="sidebar" class="fixed inset-y-0 left-0 bg-indigo-900 text-white w-64 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out z-40">
    <div class="p-6 flex items-center space-x-3 border-b border-indigo-800">
        <i class="fas fa-graduation-cap text-yellow-400 text-2xl"></i>
        <span class="text-xl font-bold tracking-wider">E-ABSENSI</span>
    </div>

    <nav class="mt-6 px-4 space-y-1">
        <a href="../<?= $role ?>/dashboard.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-800 transition group">
            <i class="fas fa-home mr-3 text-indigo-400 group-hover:text-white"></i> Dashboard
        </a>

        <?php if($role == 'admin'): ?>
            <div class="pt-4 pb-2 px-4 text-xs font-semibold text-indigo-400 uppercase tracking-wider">Master Data</div>
            <a href="data_guru.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-800 transition">
                <i class="fas fa-chalkboard-teacher mr-3 text-indigo-400"></i> Kelola Guru
            </a>
            <a href="data_jurusan.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-800 transition">
                <i class="fas fa-school mr-3 text-indigo-400"></i> Data Jurusan
            </a>
        <?php endif; ?>

        <?php if($role == 'guru'): ?>
            <div class="pt-4 pb-2 px-4 text-xs font-semibold text-indigo-400 uppercase tracking-wider">Menu Guru</div>
            <a href="data_siswa.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-800 transition">
                <i class="fas fa-user-graduate mr-3 text-indigo-400"></i> Kelola Siswa
            </a>
            <a href="generate_qr.php" class="flex items-center px-4 py-3 bg-yellow-500 text-indigo-950 font-bold rounded-lg mt-4 shadow-lg">
                <i class="fas fa-qrcode mr-3"></i> Tampilkan QR
            </a>
            <a href="laporan.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-800 transition">
                <i class="fas fa-file-invoice mr-3 text-indigo-400"></i> Laporan Absen
            </a>
            <a href="absensi_manual.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-800 transition">
                <i class="fas fa-file-invoice mr-3 text-indigo-400"></i>Absen Manual
            </a>
        <?php endif; ?>

        <?php if($role == 'siswa'): ?>
            <div class="pt-4 pb-2 px-4 text-xs font-semibold text-indigo-400 uppercase tracking-wider">Menu Siswa</div>
            <a href="scan.php" class="flex items-center px-4 py-3 bg-green-500 text-white font-bold rounded-lg shadow-lg">
                <i class="fas fa-camera mr-3"></i> SCAN ABSEN
            </a>
            <a href="riwayat.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-800 transition">
                <i class="fas fa-history mr-3 text-indigo-400"></i> Riwayat Saya
            </a>
        <?php endif; ?>

        <div class="pt-10">
            <a href="../logout.php" class="flex items-center px-4 py-3 rounded-lg text-red-300 hover:bg-red-600 hover:text-white transition">
                <i class="fas fa-sign-out-alt mr-3"></i> Logout
            </a>
        </div>
    </nav>
</aside>

<script>
    const btn = document.getElementById('btn-toggle');
    const sidebar = document.getElementById('sidebar');
    btn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });
</script>