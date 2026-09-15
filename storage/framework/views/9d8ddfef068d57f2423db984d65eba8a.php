<!DOCTYPE html>
<html lang="id" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Buat Sandi Baru — Gudang Gadget</title>
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
                    <p class="text-gold-400 font-semibold text-sm uppercase tracking-widest mb-3">Sandi baru</p>
                    <h1 class="text-3xl font-bold leading-snug">Buat sandi baru<br>untuk melanjutkan.</h1>
                    <p class="text-navy-100/80 mt-4 max-w-md leading-relaxed">
                        Pastikan sandi baru Anda kuat dan berbeda dari sebelumnya.
                    </p>
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

                    <h2 class="text-2xl font-bold text-navy-800">Buat Sandi Baru</h2>
                    <p class="text-sm text-navy-400 mt-1 mb-8">Untuk akun <span class="text-navy-700 font-semibold"><?php echo e($email); ?></span>.</p>

                    <?php if($errors->any()): ?>
                        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm flex items-start gap-2">
                            <span class="font-semibold">!</span>
                            <span><?php echo e($errors->first()); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('password.store')); ?>">
                        <div class="bg-white rounded-2xl border border-navy-100 p-6 space-y-5 shadow-sm">
                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="token" value="<?php echo e($token); ?>">
                            <input type="hidden" name="email" value="<?php echo e($email); ?>">

                            <div>
                                <label class="block text-sm font-medium text-navy-700 mb-1.5">Sandi Baru</label>
                                <input type="password" name="password" required autofocus autocomplete="new-password"
                                       placeholder="Minimal 8 karakter"
                                       class="w-full rounded-xl border border-navy-100 bg-navy-50/50 px-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-navy-700 mb-1.5">Ulangi Sandi</label>
                                <input type="password" name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Ketik ulang sandi baru"
                                       class="w-full rounded-xl border border-navy-100 bg-navy-50/50 px-4 py-2.5 text-sm placeholder-navy-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white transition-colors">
                            </div>

                            <button type="submit"
                                    class="w-full bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                                Ubah Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>
</html><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/auth/reset-password.blade.php ENDPATH**/ ?>