<header class="bg-white shadow-sm border-b px-6 py-4 flex justify-between items-center sticky top-0 z-30">
    <button id="btn-toggle" class="md:hidden text-gray-600 focus:outline-none">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <div class="hidden md:block">
        <h2 class="text-lg font-semibold text-gray-700">Panel Kendali <span class="text-indigo-600 uppercase text-sm ml-2 px-2 py-1 bg-indigo-50 rounded"><?= $_SESSION['role'] ?></span></h2>
    </div>

    <div class="flex items-center space-x-4">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-bold text-gray-800 leading-none"><?= $_SESSION['username'] ?></p>
            <p class="text-xs text-gray-500 uppercase mt-1"><?= date('d M Y') ?></p>
        </div>
        <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
            <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
        </div>
    </div>
</header>