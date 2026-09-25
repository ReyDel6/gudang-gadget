@extends('layouts.app')

@section('title', 'Data Semua Gadget')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Data Semua Gadget</h1>
        <div class="flex items-center gap-3 print-hidden">
            <select id="path-sort" onchange="location.href = this.value" class="border border-navy-100 bg-white text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500">
                <option value="{{ route('gadget.export', request()->query()) }}">Ekspor sesuai filter</option>
            </select>
            <a href="{{ route('gadget.export', request()->query()) }}"
                class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Export CSV
            </a>
            <a href="{{ route('gadget.create') }}"
                class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                + Tambah Produk
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('gadget.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5 print-hidden">
        <div class="relative flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SKU / rak / supplier..."
                class="w-full rounded-lg border border-navy-100 pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-navy-400">⌕</span>
        </div>
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
        <button type="submit"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Cari
        </button>
        @if (request()->hasAny(['search', 'kategori', 'status']))
            <a href="{{ route('gadget.index') }}"
               class="border border-navy-100 bg-white text-navy-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-navy-50">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm" style="table-layout: fixed;">
                <colgroup>
                    <col style="width: 6%">
                    <col style="width: 10%">
                    <col style="width: 17%">
                    <col style="width: 10%">
                    <col style="width: 8%">
                    <col style="width: 9%">
                    <col style="width: 11%">
                    <col style="width: 12%">
                    <col style="width: 17%">
                </colgroup>
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">No</th>
                        <th class="px-4 py-3 font-medium">SKU</th>
                        <th class="px-4 py-3 font-medium">Nama Produk</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Stok</th>
                        <th class="px-4 py-3 font-medium">Rak</th>
                        <th class="px-4 py-3 font-medium">Harga Beli</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right print-hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($data as $row)
                        <tr>
                            <td class="px-4 py-3 font-mono text-navy-400 align-top">{{ $data->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-mono text-navy-500 align-top truncate" title="{{ $row->sku }}">{{ $row->sku ?: '—' }}</td>
                            <td class="px-4 py-3 align-top font-medium text-navy-800 truncate" title="{{ $row->nama_produk }}">
                                <a href="{{ route('gadget.show', $row->id) }}"
                                   class="hover:text-gold-600 transition-colors">{{ $row->nama_produk }}</a>
                            </td>
                            <td class="px-4 py-3 align-top text-navy-600 truncate" title="{{ $row->kategori }}">{{ $row->kategori }}</td>
                            <td class="px-4 py-3 align-top">
                                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $row->stock <= 0 ? 'bg-rose-50 text-rose-600' : ($row->menipis ? 'bg-amber-50 text-amber-600' : 'bg-navy-50 text-navy-600') }}">
                                    {{ $row->stock }} {{ $row->satuan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top text-navy-600 truncate">{{ $row->lokasi_rak ?: '—' }}</td>
                            <td class="px-4 py-3 align-top text-navy-600 truncate">
                                {{ $row->harga_beli ? 'Rp ' . number_format($row->harga_beli, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-4 py-3 align-top">
                                @php
                                    $badge = match ($row->status) {
                                        'Tersedia' => 'bg-emerald-50 text-emerald-600',
                                        'Habis' => 'bg-rose-50 text-rose-600',
                                        default => 'bg-slate-100 text-slate-500',
                                    };
                                @endphp
                                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right align-top whitespace-nowrap print-hidden">
                                <a href="{{ route('gadget.barcode', $row->id) }}" class="text-navy-600 hover:text-gold-600 font-medium mr-2">Barcode</a>
                                <a href="{{ route('gadget.edit', $row->id) }}" class="text-navy-600 hover:text-gold-600 font-medium mr-3">Edit</a>
                                @if (Auth::user()->isAdmin())
                                    <form action="{{ route('gadget.destroy', $row->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Arsipkan produk {{ $row->nama_produk }}? Produk tetap bisa dipulihkan dari menu Arsip.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 font-medium">Arsip</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-navy-400">
                                Belum ada data gadget.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->hasPages())
            <div class="px-4 py-4 border-t border-navy-100 print-hidden">
                {{ $data->links() }}
            </div>
        @endif
    </div>

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

@endsection