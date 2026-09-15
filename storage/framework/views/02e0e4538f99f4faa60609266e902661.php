<?php $__env->startSection('title', $gadget->nama_produk); ?>

<?php $__env->startSection('content'); ?>

    <?php
        $badge = match ($gadget->status) {
            'Tersedia' => 'bg-emerald-50 text-emerald-600',
            'Habis' => 'bg-rose-50 text-rose-600',
            default => 'bg-slate-100 text-slate-500',
        };
    ?>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Detail Produk</h1>
        <div class="flex items-center gap-3 print-hidden">
            <a href="<?php echo e(route('gadget.index')); ?>"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Kembali
            </a>
            <a href="<?php echo e(route('gadget.edit', $gadget->id)); ?>"
               class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <div class="aspect-square rounded-xl overflow-hidden bg-navy-50 border border-navy-100">
                <?php if($gadget->thumbnail): ?>
                    <img src="<?php echo e($gadget->foto_url); ?>" alt="<?php echo e($gadget->nama_produk); ?>"
                         class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full grid place-items-center text-navy-300 text-5xl">📦</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-navy-100 p-6 md:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="text-xs font-medium text-navy-400 uppercase tracking-wide">ID #<?php echo e($gadget->id); ?></span>
                    <h2 class="text-2xl font-black text-navy-800 mt-1"><?php echo e($gadget->nama_produk); ?></h2>
                    <span class="mt-2 inline-block rounded-full px-3 py-1 text-xs font-semibold <?php echo e($badge); ?>">
                        <?php echo e($gadget->status); ?>

                    </span>
                    <span class="ml-2 inline-block rounded-full bg-navy-100 px-3 py-1 text-xs font-semibold text-navy-600">
                        <?php echo e($gadget->kategori); ?>

                    </span>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-semibold text-navy-700 mb-1">Deskripsi</h3>
                <p class="text-navy-600 whitespace-pre-line"><?php echo e($gadget->deskripsi); ?></p>
            </div>

            <div class="mt-6 p-4 rounded-xl bg-navy-50/60 border border-navy-100 print-hidden">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Stok Saat Ini</div>
                        <div class="text-3xl font-black text-navy-800 mt-1"><?php echo e($gadget->stock); ?></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="<?php echo e(route('gadget.stok', [$gadget->id, 'turun'])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" title="Kurangi stok"
                                    class="w-11 h-11 grid place-items-center rounded-xl border border-navy-200 bg-white text-navy-700 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 text-xl font-bold transition-colors">−</button>
                        </form>
                        <form action="<?php echo e(route('gadget.stok', [$gadget->id, 'naik'])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" title="Tambah stok"
                                    class="w-11 h-11 grid place-items-center rounded-xl border border-navy-200 bg-white text-navy-700 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 text-xl font-bold transition-colors">+</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mt-8 print-hidden">
        <h2 class="text-base font-bold text-navy-800 mb-4">Riwayat Mutasi Stok</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-sm">
                    <thead>
                        <tr class="bg-navy-50 text-navy-500 text-left">
                            <th class="px-4 py-3 font-medium">Waktu</th>
                            <th class="px-4 py-3 font-medium">Perubahan</th>
                            <th class="px-4 py-3 font-medium">Sebelum → Sesudah</th>
                            <th class="px-4 py-3 font-medium">Keterangan</th>
                            <th class="px-4 py-3 font-medium">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100">
                        <?php $__empty_1 = true; $__currentLoopData = $stokLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 text-navy-500"><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                                <td class="px-4 py-3">
                                    <?php if($log->perubahan > 0): ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-600 text-xs font-bold">+<?php echo e($log->perubahan); ?></span>
                                    <?php elseif($log->perubahan < 0): ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-rose-600 text-xs font-bold"><?php echo e($log->perubahan); ?></span>
                                    <?php else: ?>
                                        <span class="text-navy-400 text-xs">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-mono text-navy-700"><?php echo e($log->stok_sebelum); ?> → <?php echo e($log->stok_sesudah); ?></td>
                                <td class="px-4 py-3 text-navy-600"><?php echo e($log->keterangan); ?></td>
                                <td class="px-4 py-3 text-navy-600"><?php echo e($log->user?->name ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-navy-400">Belum ada mutasi stok.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/gadget/show.blade.php ENDPATH**/ ?>