<?php $__env->startSection('title', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('content'); ?>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Manajemen Pengguna</h1>
            <p class="text-sm text-navy-400 mt-1">Kelola akun yang dapat mengakses panel.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        
        <div class="bg-white rounded-2xl border border-navy-100 p-6 h-fit">
            <h2 class="text-base font-bold text-navy-800 mb-1">Tambah Pengguna</h2>
            <p class="text-sm text-navy-400 mb-5">Buat akun baru untuk staf gudang.</p>

            <form method="POST" action="<?php echo e(route('user.store')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Nama</label>
                    <input type="text" name="name" required value="<?php echo e(old('name')); ?>"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 <?php echo e($errors->first('name') ? 'border-rose-400' : 'border-navy-100'); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Email</label>
                    <input type="email" name="email" required value="<?php echo e(old('email')); ?>"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 <?php echo e($errors->first('email') ? 'border-rose-400' : 'border-navy-100'); ?>">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Sandi</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 <?php echo e($errors->first('password') ? 'border-rose-400' : 'border-navy-100'); ?>">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                <button type="submit" class="w-full bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Tambahkan
                </button>
            </form>
        </div>

        
        <div class="lg:col-span-2 bg-white rounded-2xl border border-navy-100 overflow-hidden">

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">ID</th>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 text-navy-400 font-mono">#<?php echo e($user->id); ?></td>
                            <td class="px-4 py-3 font-semibold text-navy-800">
                                <?php echo e($user->name); ?>

                                <?php if(auth()->id() === $user->id): ?>
                                    <span class="ml-1 text-[10px] uppercase tracking-wide bg-gold-100 text-gold-700 px-2 py-0.5 rounded-full">Anda</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-navy-600"><?php echo e($user->email); ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('user.edit', $user->id)); ?>"
                                       class="px-3 py-1.5 rounded-lg border border-navy-100 text-navy-600 hover:bg-navy-50 font-semibold text-xs">
                                        Edit
                                    </a>
                                    <?php if(auth()->id() !== $user->id): ?>
                                        <form action="<?php echo e(route('user.destroy', $user->id)); ?>" method="POST"
                                              onsubmit="return confirm('Hapus pengguna <?php echo e($user->name); ?>?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50 font-semibold text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-navy-400">Belum ada pengguna.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/users/index.blade.php ENDPATH**/ ?>