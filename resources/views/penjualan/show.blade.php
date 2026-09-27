@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Detail Penjualan</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('penjualan.index') }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Daftar
            </a>
            <a href="{{ route('penjualan.cetak', $penjualan->id) }}" target="_blank"
               class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Cetak Struk
            </a>
            @if (Auth::user()->isAdmin())
                <form action="{{ route('penjualan.destroy', $penjualan->id) }}" method="POST"
                      onsubmit="return confirm('Batalkan penjualan {{ $penjualan->no_invoice }}? Stok akan dikembalikan ke gudang.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Batal Transaksi
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-navy-400 uppercase tracking-wide">No. Invoice</p>
                <p class="text-xl font-black text-navy-800 mt-0.5">{{ $penjualan->no_invoice }}</p>
                <p class="text-sm text-navy-500 mt-1">{{ $penjualan->tanggal->format('d M Y') }} · oleh {{ $penjualan->user_name ?: '—' }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-medium text-navy-400 uppercase tracking-wide">Subtotal</p>
                <p class="text-lg font-bold text-navy-700 mt-0.5">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</p>
                @if ((float) $penjualan->diskon > 0)
                    <p class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">Diskon</p>
                    <p class="text-sm text-rose-600">− Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</p>
                @endif
                @if ((float) $penjualan->pajak > 0)
                    <p class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">PPN {{ number_format($penjualan->pajak, 0, ',', '.') }}%</p>
                    <p class="text-sm text-navy-600">Rp {{ number_format($penjualan->pajak_nominal, 0, ',', '.') }}</p>
                @endif
                <p class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">Total</p>
                <p class="text-2xl font-black text-navy-800 mt-0.5">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</p>
                <p class="text-sm text-emerald-600 font-semibold mt-1">Laba: Rp {{ number_format($penjualan->laba, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-5 text-sm">
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Customer</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $penjualan->customer ?: 'Umum' }}</div>
            </div>
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Keterangan</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $penjualan->keterangan ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Jumlah Item</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $penjualan->items->sum('qty') }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Harga Beli</th>
                        <th class="px-4 py-3 font-medium">Harga Jual</th>
                        <th class="px-4 py-3 font-medium">Qty</th>
                        <th class="px-4 py-3 font-medium">Subtotal</th>
                        <th class="px-4 py-3 font-medium">Laba</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @foreach ($penjualan->items as $item)
                        @php $laba = ((float) $item->harga_jual - (float) $item->harga_beli) * $item->qty; @endphp
                        <tr>
                            <td class="px-4 py-3 font-semibold text-navy-800">
                                {{ $item->nama_produk }}
                                <span class="block text-xs font-normal text-navy-400 mt-0.5">{{ $item->gadget?->sku ?: '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-navy-600">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-navy-800">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono text-navy-700">{{ $item->qty }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-navy-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-emerald-600">Rp {{ number_format($laba, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection