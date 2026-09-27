@extends('layouts.app')

@section('title', 'Pembelian')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Transaksi Pembelian</h1>
        <a href="{{ route('pembelian.create') }}"
           class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            + Catat Pembelian
        </a>
    </div>

    <form method="GET" action="{{ route('pembelian.index') }}" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no invoice / supplier..."
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
        <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 text-navy-700">
        <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 text-navy-700">
        <button type="submit"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Terapkan
        </button>
        @if (request()->hasAny(['search', 'tanggal_awal', 'tanggal_akhir']))
            <a href="{{ route('pembelian.index') }}"
               class="border border-navy-100 bg-white text-navy-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-navy-50">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">No. Invoice</th>
                        <th class="px-4 py-3 font-medium">Tanggal</th>
                        <th class="px-4 py-3 font-medium">Supplier</th>
                        <th class="px-4 py-3 font-medium">Item</th>
                        <th class="px-4 py-3 font-medium text-right">Total</th>
                        <th class="px-4 py-3 font-medium">Oleh</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($pembelians as $p)
                        @php $jumlahItem = $p->items->sum('qty'); @endphp
                        <tr>
                            <td class="px-4 py-3 font-mono font-semibold text-navy-800">{{ $p->no_invoice }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $p->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $p->supplier ?: '—' }}</td>
                            <td class="px-4 py-3 font-mono text-navy-700">{{ $jumlahItem }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-navy-800">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $p->user_name ?: '—' }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('pembelian.show', $p->id) }}"
                                   class="text-navy-600 hover:text-gold-600 font-medium">Detail</a>
                                <a href="{{ route('pembelian.cetak', $p->id) }}" target="_blank"
                                   class="ml-3 text-navy-600 hover:text-gold-600 font-medium">Nota</a>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="7" icon="receipt" message="Belum ada transaksi pembelian." />
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pembelians->links() }}
    </div>

@endsection