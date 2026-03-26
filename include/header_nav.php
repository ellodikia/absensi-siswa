<header class="bg-white border-b border-slate-100 px-6 py-5 flex justify-between items-center sticky top-0 z-30">
    <button id="btn-toggle" class="md:hidden w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl focus:outline-none active:scale-90 transition-all">
        <i class="fas fa-bars-staggered text-xl"></i>
    </button>

    <div class="hidden md:block">
        <div class="flex items-center gap-3">
            <span class="text-xs font-black bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg uppercase tracking-widest border border-indigo-100">
                <?= $_SESSION['role'] ?>
            </span>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-black text-slate-800 leading-none mb-1 uppercase tracking-tight"><?= $_SESSION['username'] ?></p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= date('l, d M Y') ?></p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-indigo-200 border-2 border-white">
            <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
        </div>
    </div>
</header>