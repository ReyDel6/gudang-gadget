@extends('layouts.app')

@section('title', $gadget->nama_produk)

@section('content')

    @php
        $badge = match ($gadget->status) {
            'Tersedia' => 'bg-emerald-50 text-emerald-600',
            'Habis' => 'bg-rose-50 text-rose-600',
            default => 'bg-slate-100 text-slate-500',
        };
    @endphp

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Detail Produk</h1>
        <div class="flex items-center gap-3 print-hidden">
            <a href="{{ route('gadget.index') }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Kembali
            </a>
            <a href="{{ route('gadget.barcode', $gadget->id) }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Barcode
            </a>
            <a href="{{ route('gadget.edit', $gadget->id) }}"
               class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Foto --}}
        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <div class="aspect-square rounded-xl overflow-hidden bg-navy-50 border border-navy-100">
                @if ($gadget->thumbnail)
                    <img src="{{ $gadget->foto_url }}" alt="{{ $gadget->nama_produk }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full grid place-items-center text-navy-300 text-5xl">📦</div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="bg-white rounded-2xl border border-navy-100 p-6 md:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="text-xs font-medium text-navy-400 uppercase tracking-wide">ID #{{ $gadget->id }}</span>
                    <h2 class="text-2xl font-black text-navy-800 mt-1">{{ $gadget->nama_produk }}</h2>
                    <span class="mt-2 inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                        {{ $gadget->status }}
                    </span>
                    <span class="ml-2 inline-block rounded-full bg-navy-100 px-3 py-1 text-xs font-semibold text-navy-600">
                        {{ $gadget->kategori }}
                    </span>
                    @if ($gadget->sku)
                        <span class="ml-2 inline-block rounded-full bg-gold-100 px-3 py-1 text-xs font-mono font-semibold text-gold-700">
                            SKU: {{ $gadget->sku }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Stok</div>
                    <div class="font-bold text-navy-800 mt-0.5">{{ $gadget->stock }} {{ $gadget->satuan }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Stok Minimum</div>
                    <div class="font-bold text-navy-800 mt-0.5">{{ $gadget->stok_minimum ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Lokasi Rak</div>
                    <div class="font-bold text-navy-800 mt-0.5">{{ $gadget->lokasi_rak ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Harga Beli</div>
                    <div class="font-bold text-navy-800 mt-0.5">Rp {{ number_format($gadget->harga_beli, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Nilai Aset</div>
                    <div class="font-bold text-navy-800 mt-0.5">Rp {{ number_format($gadget->nilai_aset, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Supplier</div>
                    <div class="font-bold text-navy-800 mt-0.5">{{ $gadget->supplier ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Serial Number</div>
                    <div class="font-bold text-navy-800 mt-0.5">{{ $gadget->serial_number ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Tanggal Pembelian</div>
                    <div class="font-bold text-navy-800 mt-0.5">{{ $gadget->tanggal_pembelian?->format('d M Y') ?: '—' }}</div>
                </div>
            </div>

            @if ($gadget->deskripsi)
                <div class="mt-5">
                    <h3 class="text-sm font-semibold text-navy-700 mb-1">Deskripsi</h3>
                    <p class="text-navy-600 whitespace-pre-line">{{ $gadget->deskripsi }}</p>
                </div>
            @endif

            <div class="mt-6 p-4 rounded-xl bg-navy-50/60 border border-navy-100 print-hidden">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Stok Saat Ini</div>
                        <div class="text-3xl font-black text-navy-800 mt-1">{{ $gadget->stock }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('gadget.stok', [$gadget->id, 'turun']) }}" method="POST">
                            @csrf
                            <button type="submit" title="Kurangi stok"
                                    class="w-11 h-11 grid place-items-center rounded-xl border border-navy-200 bg-white text-navy-700 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 text-xl font-bold transition-colors">−</button>
                        </form>
                        <form action="{{ route('gadget.stok', [$gadget->id, 'naik']) }}" method="POST">
                            @csrf
                            <button type="submit" title="Tambah stok"
                                    class="w-11 h-11 grid place-items-center rounded-xl border border-navy-200 bg-white text-navy-700 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 text-xl font-bold transition-colors">+</button>
                        </form>
                    </div>
                </div>
            </div>

            @if (Auth::user()->isAdmin())
                <div class="mt-4 p-4 rounded-xl border border-navy-100 divider print-hidden">
                    <form action="{{ route('gadget.transfer', $gadget->id) }}" method="POST" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div class="flex-1 min-w-[160px]">
                            <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Pindah Rak ke</label>
                            <input type="text" name="lokasi_rak_tujuan" required placeholder="cth: RAK-B2"
                                   class="w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                        <div class="w-24">
                            <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Qty</label>
                            <input type="number" name="qty" min="1" placeholder="Semua"
                                   class="w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                        <button type="submit"
                                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
                            Transfer
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Riwayat mutasi stok --}}
    <div class="mt-8 print-hidden">
        <h2 class="text-base font-bold text-navy-800 mb-4">Riwayat Mutasi Stok</h2>
        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-sm">
                    <thead>
                        <tr class="bg-navy-50 text-navy-500 text-left">
                            <th class="px-4 py-3 font-medium">Waktu</th>
                            <th class="px-4 py-3 font-medium">Tipe</th>
                            <th class="px-4 py-3 font-medium">Perubahan</th>
                            <th class="px-4 py-3 font-medium">Sebelum → Sesudah</th>
                            <th class="px-4 py-3 font-medium">Keterangan</th>
                            <th class="px-4 py-3 font-medium">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100">
                        @forelse ($stokLogs as $log)
                            <tr>
                                <td class="px-4 py-3 text-navy-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-navy-600 text-xs font-semibold">{{ $log->tipe }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($log->perubahan > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-600 text-xs font-bold">+{{ $log->perubahan }}</span>
                                    @elseif ($log->perubahan < 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-rose-600 text-xs font-bold">{{ $log->perubahan }}</span>
                                    @else
                                        <span class="text-navy-400 text-xs">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-navy-700">{{ $log->stok_sebelum }} → {{ $log->stok_sesudah }}</td>
                                <td class="px-4 py-3 text-navy-600">{{ $log->keterangan }}</td>
                                <td class="px-4 py-3 text-navy-600">{{ $log->pelaku }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-navy-400">Belum ada mutasi stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection