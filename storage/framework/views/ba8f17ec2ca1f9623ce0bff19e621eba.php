<?php $__env->startSection('title', 'Edit Pengguna'); ?>

<?php $__env->startSection('content'); ?>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Edit Pengguna</h1>
            <p class="text-sm text-navy-400 mt-1">ID #<?php echo e($user->id); ?> · <?php echo e($user->name); ?></p>
        </div>
        <a href="<?php echo e(route('user.index')); ?>"
           class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            ← Kembali
        </a>
    </div>

    <div class="max-w-2xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <form action="<?php echo e(route('user.update', $user->id)); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1.5">Nama</label>
                <input type="text" name="name" required value="<?php echo e(old('name', $user->name)); ?>"
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
                <input type="email" name="email" required value="<?php echo e(old('email', $user->email)); ?>"
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
                <label class="block text-sm font-medium text-navy-700 mb-1.5">Sandi Baru <span class="text-navy-400 font-normal">(kosongkan jika tidak diganti)</span></label>
                <input type="password" name="password" autocomplete="new-password"
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
                <label class="block text-sm font-medium text-navy-700 mb-1.5">Ulangi Sandi Baru</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                       class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan perubahan
                </button>
                <a href="<?php echo e(route('user.index')); ?>" class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/users/edit.blade.php ENDPATH**/ ?>