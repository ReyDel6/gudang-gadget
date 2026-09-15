<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $__env->yieldContent('title', 'Gudang Gadget'); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    </head>
    <body class="bg-navy-100/60 text-navy-900 min-h-screen">

        <?php
            $routeName = request()->route() ? request()->route()->getName() : '';
        ?>

        <div class="min-h-screen flex">

            
            <aside id="sidebar"
                   class="w-64 bg-navy-900 text-slate-300 flex flex-col shadow-xl transition-all duration-300 overflow-hidden print-hidden">

                
                <div class="p-6 border-b border-white/10 flex items-center gap-3 w-64">
                    <div class="p-2.5 bg-gold-500 rounded-xl text-navy-900 shadow-md">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            <path d="M12 12v3M9 13.5h6"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-black text-white text-lg tracking-tight">
                            Gudang <span class="text-gold-400">Gadget</span>
                        </h1>
                        <span class="text-xs text-slate-400 font-medium">Panel Admin</span>
                    </div>
                </div>

                
                <nav id="sidebarNav" class="flex-1 p-4 space-y-2 w-64 overflow-y-auto">
                    
                    <a href="<?php echo e(route('landing')); ?>"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                              <?php echo e($routeName === 'landing'
                                  ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                  : 'hover:bg-white/10 text-slate-400 hover:text-white'); ?>">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10"/>
                        </svg>
                        Dashboard
                    </a>

                    
                    <?php
                        $produkActive = in_array($routeName, ['gadget.index', 'gadget.create']);
                    ?>
                    <div class="space-y-1">
                        <button type="button" onclick="toggleDropdown('produk')"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                       <?php echo e($produkActive
                                           ? 'bg-white/10 text-white font-bold border-l-4 border-gold-400 pl-2.5'
                                           : 'hover:bg-white/10 text-slate-400 hover:text-white'); ?>">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 8l-9-5-9 5v8l9 5 9-5V8zM3 8l9 5 9-5M12 13v8"/>
                                </svg>
                                <span>Manajemen Produk</span>
                            </div>
                            <svg id="chevron-produk" class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>

                        <div id="submenu-produk" class="pl-6 space-y-1 pt-1 hidden">
                            <a href="<?php echo e(route('gadget.index')); ?>"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      <?php echo e($routeName === 'gadget.index'
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white'); ?>">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16v12H4zM4 10h16M4 14h16M8 6v12"/>
                                </svg>
                                Semua Produk
                            </a>
                            <a href="<?php echo e(route('gadget.create')); ?>"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      <?php echo e($routeName === 'gadget.create'
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white'); ?>">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                                Tambah Produk
                            </a>
                        </div>
                    </div>
                </nav>

                
                <div class="p-4 border-t border-white/10 w-64">
                    <a href="<?php echo e(route('landing')); ?>"
                       class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white/10 hover:bg-white/20 text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10"/>
                        </svg>
                        Buka Halaman Dashboard
                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-3">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent hover:bg-white/10 text-slate-400 hover:text-rose-300 rounded-xl text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                            Keluar Akun
                        </button>
                    </form>
                </div>
            </aside>

            
            <div class="flex-1 flex flex-col min-w-0">

                
                <header class="bg-white h-20 border-b border-navy-100 px-4 sm:px-8 flex items-center justify-between shadow-sm print-hidden z-20">
                    <div class="flex items-center gap-3">
                        <button onclick="toggleSidebar()"
                                class="p-2 hover:bg-navy-50 rounded-lg text-navy-500 transition-colors"
                                aria-label="Buka/Tutup menu">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18M3 12h18M3 18h18"/>
                            </svg>
                        </button>
                        <h2 class="text-lg sm:text-xl font-black text-navy-800 tracking-tight"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-navy-100">
                            <div class="w-10 h-10 rounded-full bg-gold-500/20 text-gold-600 font-bold flex items-center justify-center text-sm">
                                <?php echo e(strtoupper(substr(Auth::user()->name ?? 'A', 0, 1))); ?>

                            </div>
                            <div class="text-left">
                                <h4 class="text-sm font-bold text-navy-800"><?php echo e(Auth::user()->name ?? 'Admin'); ?></h4>
                                <span class="text-xs text-gold-600 font-semibold"><?php echo e(Auth::user()->email ?? ''); ?></span>
                            </div>
                        </div>

                        <button onclick="event.stopPropagation(); document.getElementById('logoutForm').submit()"
                                title="Keluar Akun"
                                class="p-2.5 bg-gold-500/10 hover:bg-gold-500/20 text-gold-600 rounded-xl transition-colors cursor-pointer">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                        </button>
                        <form id="logoutForm" method="POST" action="<?php echo e(route('logout')); ?>" class="hidden">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </header>

                
                <main class="flex-1 p-4 sm:p-8 overflow-y-auto max-w-7xl w-full mx-auto">
                    <?php if(session('success')): ?>
                        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </main>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('w-64');
                sidebar.classList.toggle('w-0');
            }

            function toggleDropdown(id) {
                const sub = document.getElementById('submenu-' + id);
                const chevron = document.getElementById('chevron-' + id);
                if (sub) {
                    sub.classList.toggle('hidden');
                    chevron.classList.toggle('rotate-180');
                }
            }

            <?php if($produkActive): ?>
                toggleDropdown('produk');
            <?php endif; ?>
        </script>

        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/layouts/app.blade.php ENDPATH**/ ?>