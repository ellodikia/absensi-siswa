<?php $role = $_SESSION['role']; ?>
<aside id="sidebar" class="fixed inset-y-0 left-0 bg-slate-900 text-white w-72 transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 shadow-2xl">
    <div class="p-8 flex items-center space-x-4 border-b border-slate-800">
        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <i class="fas fa-qrcode text-white text-xl"></i>
        </div>
        <span class="text-2xl font-black tracking-tighter">E-ABSEN</span>
    </div>

    <nav class="mt-8 px-4 space-y-2 overflow-y-auto max-h-[calc(100vh-120px)]">
        <a href="../<?= $role ?>/dashboard.php" class="flex items-center px-6 py-4 rounded-2xl hover:bg-slate-800 transition-all group font-bold">
            <i class="fas fa-th-large mr-4 text-slate-500 group-hover:text-indigo-400"></i> Dashboard
        </a>

        <?php if($role == 'admin'): ?>
            <div class="pt-6 pb-2 px-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Master Data</div>
            <a href="data_guru.php" class="flex items-center px-6 py-4 rounded-2xl hover:bg-slate-800 transition-all group font-bold">
                <i class="fas fa-chalkboard-teacher mr-4 text-slate-500 group-hover:text-indigo-400"></i> Kelola Guru
            </a>
        <?php endif; ?>

        <?php if($role == 'guru'): ?>
            <div class="pt-6 pb-2 px-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Menu Guru</div>
            <a href="data_siswa.php" class="flex items-center px-6 py-4 rounded-2xl hover:bg-slate-800 transition-all group font-bold">
                <i class="fas fa-user-graduate mr-4 text-slate-500 group-hover:text-indigo-400"></i> Kelola Siswa
            </a>
            <a href="generate_qr.php" class="flex items-center px-6 py-4 bg-indigo-600 text-white rounded-2xl mt-4 shadow-xl shadow-indigo-600/20 font-bold hover:bg-indigo-700 transition-all">
                <i class="fas fa-qrcode mr-4"></i> Tampilkan QR
            </a>
            <a href="laporan.php" class="flex items-center px-6 py-4 rounded-2xl hover:bg-slate-800 transition-all group font-bold">
                <i class="fas fa-file-alt mr-4 text-slate-500 group-hover:text-indigo-400"></i> Laporan Harian
            </a>
            <a href="laporan_bulanan.php" class="flex items-center px-6 py-4 rounded-2xl hover:bg-slate-800 transition-all group font-bold">
                <i class="fas fa-calendar-alt mr-4 text-slate-500 group-hover:text-indigo-400"></i> Rekap Bulanan
            </a>
            <a href="absensi_manual.php" class="flex items-center px-6 py-4 rounded-2xl hover:bg-slate-800 transition-all group font-bold">
                <i class="fas fa-edit mr-4 text-slate-500 group-hover:text-indigo-400"></i> Absen Manual
            </a>
        <?php endif; ?>

        <?php if($role == 'siswa'): ?>
            <div class="pt-6 pb-2 px-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Menu Siswa</div>
            <a href="scan.php" class="flex items-center px-6 py-4 bg-emerald-500 text-white rounded-2xl shadow-xl shadow-emerald-500/20 font-bold hover:bg-emerald-600 transition-all">
                <i class="fas fa-camera mr-4"></i> SCAN ABSEN
            </a>
            <a href="riwayat.php" class="flex items-center px-6 py-4 rounded-2xl hover:bg-slate-800 transition-all group font-bold">
                <i class="fas fa-history mr-4 text-slate-500 group-hover:text-indigo-400"></i> Riwayat Saya
            </a>
        <?php endif; ?>

        <div class="pt-12">
            <a href="../logout.php" class="flex items-center px-6 py-4 rounded-2xl text-red-400 hover:bg-red-500/10 hover:text-red-500 transition-all font-bold">
                <i class="fas fa-power-off mr-4"></i> Keluar
            </a>
        </div>
    </nav>
</aside>

<div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const btnToggle = document.getElementById('btn-toggle'); 

        function toggleSidebar() {
            const isHidden = sidebar.classList.contains('-translate-x-full');
            
            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => { overlay.classList.add('opacity-100'); }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => { overlay.classList.add('hidden'); }, 300);
            }
        }

        if (btnToggle) {
            btnToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
        }

        overlay.addEventListener('click', toggleSidebar);
    });
</script>