@extends('layouts.app')

@section('title', 'Penjualan Harian')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-xl font-bold text-navy-800">Rekap Penjualan Harian</h1>
        <form method="GET" action="{{ route('laporan.harian') }}" class="flex gap-2">
            <input type="date" name="tanggal" value="{{ $tanggal }}"
                   class="rounded-lg border border-navy-100 px-4 py-2.5 bg-white text-navy-700 focus:outline-none focus:ring-2 focus:ring-gold-500">
            <button type="submit"
                    class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">Lihat</button>
            <a href="{{ route('laporan.harian-export', ['tanggal' => $tanggal]) }}"
               class="border border-emerald-200 bg-white hover:bg-emerald-50 text-emerald-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">Export CSV</a>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Transaksi</p>
            <p class="text-2xl font-black text-navy-800 mt-1">{{ $data['jumlah'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Omzet Kotor</p>
            <p class="text-2xl font-black text-navy-800 mt-1">Rp {{ number_format($data['omzet_kotor'], 0, ',', '.') }}</p>
            <p class="text-xs text-navy-400 mt-0.5">Diskon −Rp {{ number_format($data['diskon'], 0, ',', '.') }} · PPN +Rp {{ number_format($data['ppn'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-200 p-4">
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide">Omzet Bersih</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($data['omzet_bersih'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Dibatalkan</p>
            <p class="text-2xl font-black text-rose-500 mt-1">{{ $data['void_jumlah'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Rerata / Transaksi</p>
            <p class="text-2xl font-black text-navy-800 mt-1">
                Rp {{ number_format($data['jumlah'] ? $data['omzet_bersih'] / $data['jumlah'] : 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <h2 class="font-black text-navy-800 mb-3">Penerimaan per Metode Bayar</h2>
            @forelse ($data['per_metode'] as $metode => $total)
                <div class="flex justify-between py-2 border-b border-navy-50 text-sm">
                    <span class="font-semibold text-navy-700">{{ $metode }}</span>
                    <span class="font-mono font-bold text-navy-800">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            @empty
                <p class="text-sm text-navy-400 text-center py-6">Belum ada transaksi pada tanggal ini.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <h2 class="font-black text-navy-800 mb-3">Top Selling</h2>
            @forelse ($top as $t)
                <div class="flex justify-between py-2 border-b border-navy-50 text-sm">
                    <span class="font-semibold text-navy-700 truncate">{{ $t->nama_produk }}</span>
                    <span class="shrink-0 ml-3 font-mono text-navy-500">{{ $t->total_qty }}<span class="text-xs"> pcs</span> · <b class="text-navy-800">Rp {{ number_format($t->total_subtotal, 0, ',', '.') }}</b></span>
                </div>
            @empty
                <p class="text-sm text-navy-400 text-center py-6">Belum ada penjualan.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <h2 class="font-black text-navy-800 mb-3">Keterangan</h2>
            <ul class="text-sm text-navy-600 space-y-2 list-disc pl-5">
                <li>Omzet kotor = subtotal sebelum diskon & pajak.</li>
                <li>Omzet bersih = total yang diterima setelah diskon dan PPN.</li>
                <li>Transaksi batal (void) dikecualikan dari perhitungan omzet & laba.</li>
                <li>Data sumber: {{ $data['tanggal'] }}.</li>
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-navy-50 flex items-center justify-between">
            <h2 class="font-black text-navy-800">Rincian Transaksi ({{ $list->count() }})</h2>
            <span class="text-xs text-navy-400 font-semibold">{{ $tanggal }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">No. Invoice</th>
                        <th class="px-4 py-3 font-medium">Jam</th>
                        <th class="px-4 py-3 font-medium">Kasir</th>
                        <th class="px-4 py-3 font-medium">Customer</th>
                        <th class="px-4 py-3 font-medium">Metode</th>
                        <th class="px-4 py-3 font-medium text-right">Total</th>
                        <th class="px-4 py-3 font-medium text-right">Dibayar</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($list as $p)
                        <tr class="{{ $p->payment_status === 'void' ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3 font-mono font-semibold text-navy-800">{{ $p->no_invoice }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $p->created_at?->format('H:i') }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $p->user_name ?: '—' }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $p->customer ?: 'Umum' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[11px] font-bold rounded-full px-2 py-0.5 bg-navy-100 text-navy-700">{{ $p->payment_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-navy-800">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-navy-600">Rp {{ number_format($p->dibayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[11px] font-bold rounded-full px-2 py-0.5 {{ $p->payment_status === 'void' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $p->status_label }}</span>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="8" icon="receipt" message="Belum ada transaksi pada tanggal ini." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection