@extends('layouts.app')

@section('title', 'Laporan Inventaris')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Laporan Inventaris</h1>
        <a href="{{ route('laporan.export', request()->query()) }}"
           class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            Export CSV
        </a>
    </div>

    <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5 print-hidden">
        <select name="kategori" onchange="this.form.submit()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Kategori</option>
            @foreach ($kategoriList as $kategori)
                <option value="{{ $kategori }}" @selected(request('kategori') === $kategori)>{{ $kategori }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Status</option>
            @foreach (['Tersedia', 'Habis', 'Tidak Dijual'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal', $periode['awal']) }}"
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 text-navy-700">
        <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', $periode['akhir']) }}"
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 text-navy-700">
        <select name="jenis" onchange="this.form.submit()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Jenis Mutasi</option>
            @foreach (['Penerimaan', 'Pengeluaran', 'Retur', 'Penyesuaian', 'Transfer', 'Stok awal'] as $jenis)
                <option value="{{ $jenis }}" @selected(request('jenis') === $jenis)>{{ $jenis }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Terapkan
        </button>
        @if (request()->hasAny(['kategori', 'status', 'tanggal_awal', 'tanggal_akhir', 'jenis']))
            <a href="{{ route('laporan.index') }}"
               class="border border-navy-100 bg-white text-navy-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-navy-50">
                Reset
            </a>
        @endif
    </form>

    <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-navy-100 p-5">
            <p class="text-gold-500 font-semibold text-xs mb-1">Total Produk</p>
            <p class="text-2xl font-bold text-navy-800">{{ number_format($totalProduk) }}</p>
            <p class="text-xs text-navy-400 mt-1">{{ $produkHabis }} habis · {{ $produkMenipis }} menipis</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-5">
            <p class="text-gold-500 font-semibold text-xs mb-1">Total Stok</p>
            <p class="text-2xl font-bold text-navy-800">{{ number_format($totalStok) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-5">
            <p class="text-gold-500 font-semibold text-xs mb-1">Nilai Aset</p>
            <p class="text-2xl font-bold text-navy-800">Rp {{ number_format($nilaiAset, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-5">
            <p class="text-gold-500 font-semibold text-xs mb-1">Mutasi (periode)</p>
            <p class="text-2xl font-bold text-emerald-600">+{{ number_format($masuk) }}</p>
            <p class="text-xs text-navy-400 mt-1"><span class="text-rose-600 font-semibold">−{{ number_format($keluar) }}</span> keluar · {{ number_format($jumlahTransaksi) }} transaksi</p>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-lg font-bold text-navy-800 mb-4">Data Produk</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-navy-50 text-navy-500 text-left">
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium">Nama Produk</th>
                            <th class="px-4 py-3 font-medium">Kategori</th>
                            <th class="px-4 py-3 font-medium">Stok</th>
                            <th class="px-4 py-3 font-medium">Harga Beli</th>
                            <th class="px-4 py-3 font-medium">Nilai Aset</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100">
                        @forelse ($products as $row)
                            <tr>
                                <td class="px-4 py-3 font-mono text-navy-500">{{ $row->sku ?: '—' }}</td>
                                <td class="px-4 py-3 font-semibold text-navy-800">{{ $row->nama_produk }}</td>
                                <td class="px-4 py-3 text-navy-600">{{ $row->kategori }}</td>
                                <td class="px-4 py-3 font-mono text-navy-700">{{ $row->stock }} {{ $row->satuan }}</td>
                                <td class="px-4 py-3 text-navy-600">Rp {{ number_format($row->harga_beli, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-mono text-navy-700">Rp {{ number_format($row->nilai_aset, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-navy-600">{{ $row->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-10 text-center text-navy-400">Tidak ada produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="px-4 py-4 border-t border-navy-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-lg font-bold text-navy-800 mb-4">Riwayat Mutasi</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="overflow-x-auto">
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
                        @forelse ($logs as $log)
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
                            <tr><td colspan="6" class="px-4 py-10 text-center text-navy-400">Tidak ada mutasi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="px-4 py-4 border-t border-navy-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </section>

@endsection