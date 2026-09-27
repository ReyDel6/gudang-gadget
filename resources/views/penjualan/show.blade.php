@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')

    @if ($penjualan->is_void)
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
            <b>Transaksi dibatalkan</b> pada {{ $penjualan->voided_at?->format('d M Y H:i') }} oleh {{ $penjualan->voided_by ?: '—' }}.
            Stok telah dikembalikan ke gudang. {{ $penjualan->void_reason ? '(' . $penjualan->void_reason . ')' : '' }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Detail Penjualan</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('penjualan.index') }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Daftar
            </a>
            @if (!$penjualan->is_void)
                <a href="{{ route('penjualan.cetak', $penjualan->id) }}" target="_blank"
                   class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                    Cetak Struk
                </a>
                @if ($penjualan->customer_phone)
                    @php
                        $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $penjualan->customer_phone)
                            . '?text=' . rawurlencode('Halo ' . ($penjualan->customer ?: 'Bapak/Ibu') . ', terima kasih telah berbelanja di Gudang Gadget.' . PHP_EOL . 'No. Nota: ' . $penjualan->no_invoice . PHP_EOL . 'Total: Rp ' . number_format($penjualan->total, 0, ',', '.'));
                    @endphp
                    <a href="{{ $wa }}" target="_blank"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Kirim Struk via WA
                    </a>
                @endif
                @if (Auth::user()->isAdmin())
                    <button type="button" onclick="document.getElementById('modalVoid').classList.remove('hidden')"
                            class="border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Batal / Retur
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-navy-400 uppercase tracking-wide">No. Invoice</p>
                <p class="text-xl font-black text-navy-800 mt-0.5">{{ $penjualan->no_invoice }}</p>
                <p class="text-sm text-navy-500 mt-1">{{ $penjualan->tanggal->format('d M Y') }} · oleh {{ $penjualan->user_name ?: '—' }}</p>
                <span class="inline-block mt-2 text-[11px] font-bold rounded-full px-2.5 py-1 {{ $penjualan->is_void ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $penjualan->status_label }}</span>
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
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5 text-sm">
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Customer</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $penjualan->customer ?: 'Umum' }}</div>
                @if ($penjualan->customer_phone)
                    <div class="text-xs text-navy-500 mt-0.5">{{ $penjualan->customer_phone }}</div>
                @endif
            </div>
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Metode Bayar</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $penjualan->payment_label }}</div>
                @if ($penjualan->payment_ref)
                    <div class="text-xs text-navy-500 mt-0.5">Ref: {{ $penjualan->payment_ref }}</div>
                @endif
            </div>
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Dibayar</div>
                <div class="font-semibold text-navy-800 mt-0.5">Rp {{ number_format($penjualan->dibayar, 0, ',', '.') }}</div>
                @if ($penjualan->kembalian > 0)
                    <div class="text-xs text-emerald-600 mt-0.5">Kembalian: Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</div>
                @endif
            </div>
            <div>
                <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Jumlah Item</div>
                <div class="font-semibold text-navy-800 mt-0.5">{{ $penjualan->items->sum('qty') }}</div>
                <div class="text-xs text-navy-500 mt-0.5">{{ $penjualan->keterangan ?: '' }}</div>
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

<div id="modalVoid" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-navy-900/60 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 pt-5 pb-2 border-b border-navy-100">
                <h3 class="font-black text-navy-800">Batal / Retur Transaksi</h3>
                <p class="text-sm text-navy-500 mt-0.5">Stok dari <b>{{ $penjualan->no_invoice }}</b> akan dikembalikan ke gudang. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <form action="{{ route('penjualan.destroy', $penjualan->id) }}" method="POST">
                @csrf @method('DELETE')
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-navy-700 mb-1">Alasan (opsional)</label>
                        <input type="text" name="void_reason" maxlength="255" placeholder="cth: salah input / retur barang"
                               class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-700 mb-1">Password Admin Kasir *</label>
                        <input type="password" name="password" required autocomplete="current-password"
                               class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-300">
                        <p class="text-xs text-navy-400 mt-1">Wajib password kasir saat ini untuk konfirmasi (syarat PRD).</p>
                    </div>
                    @if ($errors->has('password') || $errors->has('void_reason'))
                        <div class="text-rose-600 text-sm">{{ $errors->first() }}</div>
                    @endif
                </div>
                <div class="px-6 py-4 border-t border-navy-100 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalVoid').classList.add('hidden')"
                            class="border border-navy-200 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Konfirmasi Batal / Retur
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection