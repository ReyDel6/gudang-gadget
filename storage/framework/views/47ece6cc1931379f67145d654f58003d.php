<?php $__env->startSection('title', 'Data Semua Gadget'); ?>

<?php $__env->startSection('content'); ?>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Data Semua Gadget</h1>
        <div class="flex items-center gap-3 print-hidden">
            <button type="button" onclick="window.print()"
                class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Generate Report
            </button>
            <a href="<?php echo e(route('gadget.export')); ?>?t=<?php echo e(time()); ?>"
                class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Export CSV
            </a>
            <a href="<?php echo e(route('gadget.create')); ?>"
                class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                + Tambah Produk
            </a>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-5 print-hidden">
        <div class="relative flex-1 max-w-sm">
            <input type="text" id="searchInput" oninput="filterTable()" placeholder="Search For..."
                class="w-full rounded-lg border border-navy-100 pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-navy-400">⌕</span>
        </div>
        <select id="kategoriFilter" onchange="filterTable()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Kategori</option>
            <?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($kategori); ?>"><?php echo e($kategori); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-sm" style="table-layout: fixed;">
                <colgroup>
                    <col style="width: 7%">
                    <col style="width: 12%">
                    <col style="width: 18%">
                    <col style="width: 12%">
                    <col style="width: 25%">
                    <col style="width: 8%">
                    <col style="width: 10%">
                </colgroup>
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">No</th>
                        <th class="px-4 py-3 font-medium print-hidden">Foto Produk</th>
                        <th class="px-4 py-3 font-medium">Nama Produk</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Deskripsi</th>
                        <th class="px-4 py-3 font-medium">Stock</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right print-hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-navy-100">
                    <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-search="<?php echo e(strtolower($row->nama_produk . ' ' . $row->kategori . ' ' . $row->status)); ?>"
                                data-kategori="<?php echo e($row->kategori); ?>">
                            <td class="px-4 py-3 font-mono text-navy-400 align-top"><?php echo e($loop->iteration); ?></td>
                            <td class="px-4 py-3 align-top print-hidden">
                                <?php if($row->thumbnail): ?>
                                    <img src="<?php echo e($row->foto_url); ?>" alt="<?php echo e($row->nama_produk); ?>"
                                        class="h-12 w-12 rounded-lg border border-navy-100 object-cover">
                                <?php else: ?>
                                    <span class="text-navy-300 text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top font-medium text-navy-800 truncate"
                                title="<?php echo e($row->nama_produk); ?>">
                                <a href="<?php echo e(route('gadget.show', $row->id)); ?>"
                                   class="hover:text-gold-600 transition-colors"><?php echo e($row->nama_produk); ?></a>
                            </td>
                            <td class="px-4 py-3 align-top text-navy-600 truncate" title="<?php echo e($row->kategori); ?>">
                                <?php echo e($row->kategori); ?></td>
                            <td class="px-4 py-3 text-navy-600 align-top whitespace-normal break-words"
                                title="<?php echo e($row->deskripsi); ?>"><?php echo e($row->deskripsi); ?></td>
                            <td class="px-4 py-3 align-top">
                                <span
                                    class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e((int) $row->stock <= 0 ? 'bg-rose-50 text-rose-600' : 'bg-navy-50 text-navy-600'); ?>">
                                    <?php echo e($row->stock); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <?php
                                    $badge = match ($row->status) {
                                        'Tersedia' => 'bg-emerald-50 text-emerald-600',
                                        'Habis' => 'bg-rose-50 text-rose-600',
                                        default => 'bg-slate-100 text-slate-500',
                                    };
                                ?>
                                <span
                                    class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($badge); ?> whitespace-normal break-words">
                                    <?php echo e($row->status); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-right align-top whitespace-nowrap print-hidden">
                                <a href="<?php echo e(route('gadget.edit', $row->id)); ?>"
                                    class="text-navy-600 hover:text-gold-600 font-medium mr-3">Edit</a>
                                <form action="<?php echo e(route('gadget.destroy', $row->id)); ?>" method="POST" class="inline"
                                    onsubmit="return confirm('Apakah yakin data akan di hapus?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                        class="text-rose-600 hover:text-rose-700 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-navy-400">
                                Belum ada data gadget.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p id="emptyState" class="hidden px-5 py-10 text-center text-navy-400">
            Tidak ada data yang cocok dengan pencarian.
        </p>
    </div>

    <script>
        function filterTable() {
            const term = document.getElementById('searchInput').value.trim().toLowerCase();
            const jenis = document.getElementById('kategoriFilter').value;
            const rows = document.querySelectorAll('#tableBody tr[data-search]');
            let visibleCount = 0;

            rows.forEach(row => {
                const matchText = row.dataset.search.includes(term);
                const matchKategori = !jenis || row.dataset.kategori === jenis;
                const match = matchText && matchKategori;
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            document.getElementById('emptyState').classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }
    </script>

    <style>
        @media print {
            .print-hidden {
                display: none !important;
            }

            @page {
                size: landscape;
                margin: 1.5cm;
            }

            body {
                background: white !important;
            }

            main {
                max-width: none !important;
                padding: 0 !important;
            }

            table {
                min-width: 0 !important;
                width: 100% !important;
                font-size: 10px !important;
            }

            th,
            td {
                padding: 8px !important;
                vertical-align: top !important;
            }
        }
    </style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\WEB\web gudang\registrasi-siswa\resources\views/gadget/index.blade.php ENDPATH**/ ?>