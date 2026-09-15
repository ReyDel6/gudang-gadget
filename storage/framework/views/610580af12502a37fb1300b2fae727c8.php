<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login — Gudang Gadget</title>
        <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    </head>
    <body class="h-full bg-navy-900 font-sans text-navy-900">

        <div class="flex min-h-screen">

            
            <div class="hidden lg:flex w-1/2 relative overflow-hidden bg-navy-800 text-white flex-col justify-between p-12">
                <div class="absolute inset-0 opacity-40" style="background:
                    radial-gradient(600px 300px at 80% -10%, rgba(232,163,61,0.35), transparent 60%),
                    radial-gradient(500px 400px at -10% 110%, rgba(61,100,145,0.55), transparent 60%);"></div>

                <div class="relative">
                    <div class="flex items-center gap-2">
                        <div class="grid place-items-center w-10 h-10 rounded-xl bg-gold-500 text-navy-900">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                <path d="M12 12v3M9 13.5h6"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight">Gudang <span class="text-gold-400">Gadget</span></span>
                    </div>
                </div>

                <div class="relative">
                    <p class="text-gold-400 font-semibold text-sm uppercase tracking-widest mb-3">Selamat datang kembali</p>
                    <h1 class="text-3xl font-bold leading-snug">
                        Kelola inventaris gadget<br>dalam satu tempat.
                    </h1>
                    <p class="text-navy-100/80 mt-4 max-w-md leading-relaxed">
                        Pantau stok, kategori, dan status produk secara real-time. Data aman,
                        pengelolaan mudah, laporan cepat.
                    </p>
                </div>

                <div class="relative flex items-center gap-2 text-sm text-navy-100/60">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Akses terproteksi
                </div>
            </div>

            
            <div class="flex-1 flex items-center justify-center bg-navy-50 px-6 py-12">
                <div class="w-full max-w-sm">

                    
                    <div class="lg:hidden flex items-center justify-center gap-2 mb-8">
                        <div class="grid place-items-center w-10 h-10 rounded-xl bg-navy-700 text-gold-400">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                <path d="M12 12v3M9 13.5h6"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-navy-800">Gudang <span class="text-gold-500">Gadget</span></span>
                    </div>

                    <h2 class="text-2xl font-bold text-navy-800">Masuk</h2>
                    <p class="text-sm text-navy-400 mt-1 mb-8">Silakan masuk ke akun Anda untuk melanjutkan.</p>

                    <?php if(session('success')): ?>
                        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm flex items-center gap-2">
                            <span>✓</span><?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm flex items-start gap-2">
                            <span class="font-semibold">!</span>
                            <span><?php echo e($errors->first()); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('login')); ?>">

                        <div class="bg-white rounded-2xl border border-navy-100 p-6 space-y-5 shadow-sm">
                            <?php echo csrf_field(); ?>

                            <div>
                                <label class="block text-sm font-medium text-navy-700 mb-1.5">Email</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                                            <path d="m22 7-10 6L2 7"/>
                                        </svg>
                                    </span>
                                    <input type="email" name="email" required value="<?php echo e(old('email')); ?>" autofocus autocomplete="email"
                                           placeholder="nama@email.com"
                                           class="w-full rounded-xl border border-navy-100 bg-navy-50/50 pl-10 pr-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                                </div>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-navy-700 mb-1.5">Password</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-300">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    </span>
                                    <input type="password" name="password" required autocomplete="current-password"
                                           placeholder="••••••••"
                                           class="w-full rounded-xl border border-navy-100 bg-navy-50/50 pl-10 pr-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-navy-600">
                                <input type="checkbox" name="remember"
                                       class="h-4 w-4 rounded border-navy-200 text-gold-500 focus:ring-gold-500">
                                Ingat saya
                            </label>

                            <button type="submit"
                                    class="w-full bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                                Masuk
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M13 6l6 6-6 6"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <p class="text-sm text-navy-500 text-center mt-6">
                        Belum punya akun?
                        <a href="<?php echo e(route('register')); ?>" class="font-semibold text-gold-600 hover:text-gold-500">Daftar</a>
                    </p>
                </div>
            </div>
        </div>

    </body>
</html><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/auth/login.blade.php ENDPATH**/ ?>