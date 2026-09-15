<?php $__env->startSection('title', 'Formulir Tambah Produk'); ?>

<?php $__env->startSection('content'); ?>

    <div class="max-w-2xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <h1 class="text-xl font-bold text-navy-800">Formulir Tambah Produk baru</h1>
        <p class="text-sm text-navy-400 mt-1 mb-6">Semua kolom wajib diisi.</p>

        <form action="<?php echo e(route('gadget.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Nama Produk</label>
                <input type="text" name="nama_produk" required value="<?php echo e(old('nama_produk')); ?>"
                       class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 <?php echo e($errors->first('nama_produk') ? 'border-rose-400' : 'border-navy-100'); ?>">
                <?php $__errorArgs = ['nama_produk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Kategori</label>
                    <select name="kategori" required
                            class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                        <?php $__currentLoopData = ['SmartPhone', 'Laptop', 'Tablet', 'SmartWatch']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kategori); ?>" <?php if(old('kategori') == $kategori): echo 'selected'; endif; ?>><?php echo e($kategori); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['kategori'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Stock</label>
                    <input type="number" name="stock" required min="0" value="<?php echo e(old('stock')); ?>"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 <?php echo e($errors->first('stock') ? 'border-rose-400' : 'border-navy-100'); ?>">
                    <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Status</label>
                    <select name="status" required
                            class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                        <?php $__currentLoopData = ['Tersedia', 'Habis', 'Tidak Dijual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php if(old('status') == $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Foto Produk</label>
                    <input type="file" name="foto" accept="image/*"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <?php $__errorArgs = ['foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" required rows="4"
                          class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 <?php echo e($errors->first('deskripsi') ? 'border-rose-400' : 'border-navy-100'); ?>"><?php echo e(old('deskripsi')); ?></textarea>
                <?php $__errorArgs = ['deskripsi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan
                </button>
                <a href="<?php echo e(route('gadget.index')); ?>"
                   class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\web gudang\registrasi-siswa\resources\views/gadget/create.blade.php ENDPATH**/ ?>