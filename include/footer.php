<footer class="bg-slate-950 border-t border-white/5 py-12 mt-auto relative overflow-hidden w-full">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-px bg-gradient-to-r from-transparent via-amber-500/50 to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-center gap-10">
            
            <div class="text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-4 mb-4">
                    <div class="w-10 h-10 bg-red-700 rotate-45 flex items-center justify-center shadow-lg shadow-red-900/40 border border-white/10">
                        <div class="-rotate-45">
                            <i class="fas fa-qrcode text-white text-xl"></i>
                        </div>
                    </div>
                    <span class="font-black text-2xl tracking-tighter text-white uppercase">
                        QR<span class="text-amber-500">-Absen</span>
                    </span>
                </div>
                <p class="text-slate-500 text-[10px] uppercase tracking-[0.3em] font-bold">
                    &copy; <?= date('Y'); ?> QR-ABSEN &bull; Sistem Absensi Digital
                </p>
            </div>

            <div class="text-center md:text-right">
                <p class="text-slate-500 text-[10px] uppercase tracking-[0.2em] mb-2 font-bold opacity-60">Engineered By</p>
                <a href="http://ellodikia.ct.ws" target="_blank" class="group flex items-center justify-center md:justify-end gap-3 text-slate-300 hover:text-amber-500 transition-all duration-500">
                    <div class="flex flex-col items-end leading-none">
                        <span class="font-black text-lg tracking-tighter uppercase group-hover:tracking-normal transition-all">ELLODIKIA</span>
                    </div>
                </a>
            </div>

        </div>

        <div class="mt-12 flex items-center gap-4">
            <div class="h-px flex-1 bg-gradient-to-r from-transparent to-slate-800"></div>
            <div class="flex gap-2">
                <div class="w-1 h-1 rounded-full bg-red-700"></div>
                <div class="w-1 h-1 rounded-full bg-amber-500"></div>
                <div class="w-1 h-1 rounded-full bg-red-700"></div>
            </div>
            <div class="h-px flex-1 bg-gradient-to-l from-transparent to-slate-800"></div>
        </div>
    </div>
</footer>