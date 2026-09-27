@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Detail Pembelian</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('pembelian.index') }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Daftar
            </a>
            <a href="{{ route('pembelian.cetak', $pembelian->id) }}" target="_blank"
               class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Cetak Nota
            </a>
            @if (Auth::user()->isAdmin())
                <form action="{{ route('pembelian.destroy', $pembelian->id) }}" method="POST"
                      onsubmit="return confirm('Batalkan pembelian {{ $pembelian->no_invoice }}? Stok akan dikurangi kembali.')">
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
                <p class="text-xl font-black text-navy-800 mt-0.5">{{ $pembelian->no_invoice }}</p>
                <p class="text-sm text-navy-500 mt-1">{{ $pembelian->tanggal->format('d M Y') }} · oleh {{ $pembelian->user_name ?: '—' }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-medium text-navy-400 uppercase tracking-wide">Subtotal</p>
                <p class="text-lg font-bold text-navy-700 mt-0.5">Rp {{ number_format($pembelian->subtotal, 0, ',', '.') }}</p>
                @if ((float) $pembelian->diskon > 0)
                    <p class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">Diskon</p>
                    <p class="text-sm text-rose-600">− Rp {{ number_format($pembelian->diskon, 0, ',', '.') }}</p>
                @endif
                @if ((float) $pembelian->pajak > 0)
                    <p class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">PPN {{ number_format($pembelian->pajak, 0, ',', '.') }}%</p>
                    <p class="text-sm text-navy-600">Rp {{ number_format($pembelian->pajak_nominal, 0, ',', '.') }}</p>
                @endif
                <p class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">Total Pembelian</p>
                <p class="text-2xl font-black text-navy-800 mt-0.5">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-5 text-sm">
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Supplier</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $pembelian->supplier ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Keterangan</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $pembelian->keterangan ?: '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Jumlah Item</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $pembelian->items->sum('qty') }}</div>
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
                        <th class="px-4 py-3 font-medium">Qty</th>
                        <th class="px-4 py-3 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @foreach ($pembelian->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-navy-800">
                                {{ $item->nama_produk }}
                                <span class="block text-xs font-normal text-navy-400 mt-0.5">{{ $item->gadget?->sku ?: '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-navy-600">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono text-navy-700">{{ $item->qty }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-navy-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection