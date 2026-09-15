<?php $__env->startSection('title', 'Dashboard Gudang Gadget'); ?>

<?php $__env->startSection('content'); ?>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Dashboard</h1>
            <p class="text-sm text-navy-400 mt-1">Ringkasan inventaris gudang gadget.</p>
        </div>
        <a href="<?php echo e(route('gadget.create')); ?>"
            class="bg-gold-500 hover:bg-gold-600 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
            + Tambah Produk
        </a>
    </div>

    <section class="grid md:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-navy-100 p-6">
            <p class="text-gold-500 font-semibold text-sm mb-1">Total Produk</p>
            <p class="text-3xl font-bold text-navy-800"><?php echo e($totalProduk); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-6">
            <p class="text-gold-500 font-semibold text-sm mb-1">Total Stok</p>
            <p class="text-3xl font-bold text-navy-800"><?php echo e($totalStock); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-6">
            <p class="text-gold-500 font-semibold text-sm mb-1">Jumlah Kategori</p>
            <p class="text-3xl font-bold text-navy-800"><?php echo e($kategori); ?></p>
        </div>
    </section>

    <?php if($stokRendah > 0): ?>
        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            <span class="font-semibold"><?php echo e($stokRendah); ?> produk habis stok</span> — segera tambah stok pada halaman edit.
        </div>
    <?php endif; ?>

    <section class="grid lg:grid-cols-2 gap-5 mt-8">
        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="text-lg font-bold text-navy-800 mb-1">Stok per Kategori</h2>
            <p class="text-sm text-navy-400 mb-4">Distribusi stok berdasarkan kategori produk.</p>
            <div class="h-64">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="text-lg font-bold text-navy-800 mb-1">Jumlah Produk per Kategori</h2>
            <p class="text-sm text-navy-400 mb-4">Banyaknya produk tiap kategori.</p>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-lg font-bold text-navy-800 mb-4">Produk Terbaru</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Nama Produk</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Stok</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-navy-800"><?php echo e($row->nama_produk); ?></td>
                        <td class="px-4 py-3 text-navy-600"><?php echo e($row->kategori); ?></td>
                        <td class="px-4 py-3 text-navy-600"><?php echo e($row->stock); ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full bg-navy-50 px-2.5 py-1 text-xs font-medium text-navy-600">
                                <?php echo e($row->status); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="<?php echo e(route('gadget.edit', $row->id)); ?>"
                               class="text-navy-600 hover:text-gold-600 font-medium">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-navy-400">
                            Belum ada produk. Mulai tambahkan produk pertama.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="mt-8 print-hidden">
        <h2 class="text-lg font-bold text-navy-800 mb-4">Aktivitas Stok Terbaru</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Perubahan</th>
                        <th class="px-4 py-3 font-medium">Stok Akhir</th>
                        <th class="px-4 py-3 font-medium">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 text-navy-500 whitespace-nowrap"><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                            <td class="px-4 py-3 font-semibold text-navy-800"><?php echo e($log->gadget?->nama_produk); ?></td>
                            <td class="px-4 py-3">
                                <?php if($log->perubahan > 0): ?>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-600 text-xs font-bold">+<?php echo e($log->perubahan); ?></span>
                                <?php elseif($log->perubahan < 0): ?>
                                    <span class="rounded-full bg-rose-50 px-2.5 py-0.5 text-rose-600 text-xs font-bold"><?php echo e($log->perubahan); ?></span>
                                <?php else: ?>
                                    <span class="text-navy-400 text-xs">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-mono text-navy-700"><?php echo e($log->stok_sesudah); ?></td>
                            <td class="px-4 py-3 text-navy-600"><?php echo e($log->user?->name ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-navy-400">Belum ada aktivitas stok.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        window.__dashData = <?php echo json_encode($chartData, 15, 512) ?>;
    </script>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/dashboard.js'); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/landing.blade.php ENDPATH**/ ?>