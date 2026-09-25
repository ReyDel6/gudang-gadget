@extends('layouts.app')

@section('title', 'Dashboard Gudang Gadget')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Dashboard</h1>
            <p class="text-sm text-navy-400 mt-1">Ringkasan inventaris gudang gadget.</p>
        </div>
        <a href="{{ route('gadget.create') }}"
            class="bg-gold-500 hover:bg-gold-600 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
            + Tambah Produk
        </a>
    </div>

    <section class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl border border-navy-100 p-6">
            <p class="text-gold-500 font-semibold text-sm mb-1">Total Produk</p>
            <p class="text-3xl font-bold text-navy-800">{{ number_format($totalProduk) }}</p>
            <p class="text-xs text-navy-400 mt-1">{{ $kategori }} kategori</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-6">
            <p class="text-gold-500 font-semibold text-sm mb-1">Total Stok</p>
            <p class="text-3xl font-bold text-navy-800">{{ number_format($totalStock) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-6">
            <p class="text-gold-500 font-semibold text-sm mb-1">Nilai Aset</p>
            <p class="text-3xl font-bold text-navy-800">Rp {{ number_format($nilaiAset, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-6">
            <p class="text-gold-500 font-semibold text-sm mb-1">Perhatian</p>
            <p class="text-3xl font-bold text-rose-600">{{ $habis }}</p>
            <p class="text-xs text-navy-400 mt-1">{{ $menipis }} produk menipis</p>
        </div>
    </section>

    @if ($habis > 0)
        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            <span class="font-semibold">{{ $habis }} produk habis stok</span> — segera tambah stok pada halaman Mutasi.
        </div>
    @endif

    <section class="grid lg:grid-cols-2 gap-5 mt-8">
        <div class="bg-white rounded-2xl border border-navy-100 p-6 print-hidden">
            <h2 class="text-lg font-bold text-navy-800 mb-1">Stok per Kategori</h2>
            <p class="text-sm text-navy-400 mb-4">Distribusi stok berdasarkan kategori produk.</p>
            <div class="h-64">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-6 print-hidden">
            <h2 class="text-lg font-bold text-navy-800 mb-1">Jumlah Produk per Kategori</h2>
            <p class="text-sm text-navy-400 mb-4">Banyaknya produk tiap kategori.</p>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-lg font-bold text-navy-800 mb-4">Produk Menipis / Habis</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Nama Produk</th>
                        <th class="px-4 py-3 font-medium">SKU</th>
                        <th class="px-4 py-3 font-medium">Stok</th>
                        <th class="px-4 py-3 font-medium">Stok Min.</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($lowStock as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium text-navy-800">{{ $row->nama_produk }}</td>
                        <td class="px-4 py-3 font-mono text-navy-500">{{ $row->sku ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $row->habis ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' }}">
                                {{ $row->stock }} {{ $row->satuan }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-navy-600">{{ $row->stok_minimum ?? '—' }}</td>
                        <td class="px-4 py-3 text-navy-600">{{ $row->status }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('gadget.show', $row->id) }}"
                               class="text-navy-600 hover:text-gold-600 font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-navy-400">
                            Tidak ada produk yang menipis atau habis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                    @forelse ($recent as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium text-navy-800">{{ $row->nama_produk }}</td>
                        <td class="px-4 py-3 text-navy-600">{{ $row->kategori }}</td>
                        <td class="px-4 py-3 text-navy-600">{{ $row->stock }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full bg-navy-50 px-2.5 py-1 text-xs font-medium text-navy-600">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('gadget.edit', $row->id) }}"
                               class="text-navy-600 hover:text-gold-600 font-medium">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-navy-400">
                            Belum ada produk. Mulai tambahkan produk pertama.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Aktivitas stok terbaru --}}
    <section class="mt-8 print-hidden">
        <h2 class="text-lg font-bold text-navy-800 mb-4">Aktivitas Stok Terbaru</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Tipe</th>
                        <th class="px-4 py-3 font-medium">Perubahan</th>
                        <th class="px-4 py-3 font-medium">Stok Akhir</th>
                        <th class="px-4 py-3 font-medium">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($recentLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-navy-500 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-navy-800">{{ $log->gadget?->nama_produk }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-navy-600 text-xs font-semibold">{{ $log->tipe }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($log->perubahan > 0)
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-600 text-xs font-bold">+{{ $log->perubahan }}</span>
                                @elseif ($log->perubahan < 0)
                                    <span class="rounded-full bg-rose-50 px-2.5 py-0.5 text-rose-600 text-xs font-bold">{{ $log->perubahan }}</span>
                                @else
                                    <span class="text-navy-400 text-xs">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-navy-700">{{ $log->stok_sesudah }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $log->pelaku }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-navy-400">Belum ada aktivitas stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        window.__dashData = @json($chartData);
    </script>
    @vite('resources/js/dashboard.js')
@endpush