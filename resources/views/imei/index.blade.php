@extends('layouts.app')

@section('title', 'Pelacakan IMEI')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Pelacakan IMEI / Serial Number</h1>
        <a href="{{ route('pembelian.create') }}"
           class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            + Catat Barang Masuk + IMEI
        </a>
    </div>

    <form method="GET" action="{{ route('imei.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5 print-hidden">
        <div class="relative flex-1 max-w-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari IMEI / serial / nama produk / SKU..."
                class="w-full rounded-lg border border-navy-100 pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-navy-400">⌕</span>
        </div>
        <select name="status" onchange="this.form.submit()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Status</option>
            @foreach (\App\Models\GadgetImei::STATUS as $kode => $label)
                <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Cari
        </button>
        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('imei.index') }}"
               class="border border-navy-100 bg-white text-navy-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-navy-50">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">IMEI / Serial</th>
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Masuk</th>
                        <th class="px-4 py-3 font-medium">Masa Garansi</th>
                        <th class="px-4 py-3 font-medium">Terjual</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($imeis as $imei)
                        <tr>
                            <td class="px-4 py-3 font-mono text-navy-800 font-semibold">
                                {{ $imei->imei }}
                                @if ($imei->serial_number)
                                    <div class="text-[10px] text-navy-400 font-mono">seri: {{ $imei->serial_number }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('gadget.show', $imei->gadget_id) }}" class="hover:text-gold-600 text-navy-800 font-semibold">
                                    {{ $imei->gadget?->nama_produk }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $warna = [
                                        'available' => 'bg-emerald-50 text-emerald-700',
                                        'sold' => 'bg-navy-50 text-navy-600',
                                        'in_service' => 'bg-gold-100 text-gold-700',
                                        'defective' => 'bg-rose-50 text-rose-600',
                                        'returned' => 'bg-sky-50 text-sky-700',
                                    ];
                                @endphp
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $warna[$imei->status] ?? 'bg-navy-50 text-navy-600' }}">
                                    {{ $imei->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-navy-500 whitespace-nowrap">
                                {{ $imei->masuk_at?->format('d M Y') ?: '-' }}
                                <span class="block text-[10px] text-navy-400">{{ $imei->masuk_via ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-navy-600 whitespace-nowrap">
                                @if ($imei->warranty_expired_at)
                                    {{ $imei->warranty_expired_at->format('d M Y') }}
                                @else
                                    <span class="text-navy-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-navy-600 whitespace-nowrap">
                                @if ($imei->sold_at)
                                    {{ $imei->sold_at->format('d M Y') }}
                                    <span class="block text-[10px] text-navy-400">{{ $imei->penjualanItem?->penjualan?->no_invoice }}</span>
                                @else
                                    <span class="text-navy-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('imei.show', $imei->id) }}"
                                   class="text-gold-600 hover:text-gold-700 font-semibold text-xs">Riwayat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-navy-400">
                                Belum ada data IMEI. Catat daftar IMEI saat barang masuk (pembelian) atau saat transaksi kasir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-navy-100">
            {{ $imeis->links() }}
        </div>
    </div>

@endsection