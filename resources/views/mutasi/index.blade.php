@extends('layouts.app')

@section('title', 'Riwayat Mutasi Stok')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Riwayat Mutasi Stok</h1>
        <a href="{{ route('mutasi.create') }}"
           class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            + Catat Mutasi
        </a>
    </div>

    <form method="GET" action="{{ route('mutasi.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5 print-hidden">
        <div class="relative flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk / SKU..."
                class="w-full rounded-lg border border-navy-100 pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-navy-400">⌕</span>
        </div>
        <select name="tipe" onchange="this.form.submit()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Tipe</option>
            @foreach (['Penerimaan', 'Pengeluaran', 'Retur', 'Penyesuaian', 'Transfer', 'Stok awal'] as $tipe)
                <option value="{{ $tipe }}" @selected(request('tipe') === $tipe)>{{ $tipe }}</option>
            @endforeach
        </select>
        <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 text-navy-700">
        <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 text-navy-700">
        <button type="submit"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Cari
        </button>
        @if (request()->hasAny(['search', 'tipe', 'tanggal_awal', 'tanggal_akhir']))
            <a href="{{ route('mutasi.index') }}"
               class="border border-navy-100 bg-white text-navy-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-navy-50">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Tipe</th>
                        <th class="px-4 py-3 font-medium">Perubahan</th>
                        <th class="px-4 py-3 font-medium">Stok Akhir</th>
                        <th class="px-4 py-3 font-medium">Keterangan</th>
                        <th class="px-4 py-3 font-medium">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-navy-500 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-navy-800">
                                <a href="{{ route('gadget.show', $log->gadget_id) }}" class="hover:text-gold-600">{{ $log->gadget?->nama_produk }}</a>
                            </td>
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
                            <td class="px-4 py-3 text-navy-600 max-w-[280px] truncate" title="{{ $log->keterangan }}">{{ $log->keterangan }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $log->pelaku }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-navy-400">Belum ada mutasi stok.</td>
                        </tr>
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

@endsection