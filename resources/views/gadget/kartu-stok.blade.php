@extends('layouts.app')

@section('title', 'Kartu Stok — ' . $gadget->nama_produk)

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Kartu Stok</h1>
            <p class="text-sm text-navy-400 mt-1">
                {{ $gadget->nama_produk }}
                @if ($gadget->sku) · <span class="font-mono">{{ $gadget->sku }}</span> @endif
                · Stok saat ini <span class="font-bold text-navy-700">{{ $gadget->stock }} {{ $gadget->satuan }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('gadget.show', $gadget->id) }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Detail Produk
            </a>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-navy-100 p-4 mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="{{ $dari }}"
                   class="rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ $sampai }}"
                   class="rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        </div>
        <button type="submit" class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            Filter
        </button>
        <a href="{{ route('gadget.kartu-stok', $gadget->id) }}" class="text-sm text-navy-600 hover:text-gold-600 px-3 py-2">
            Reset
        </a>
    </form>

    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-navy-100 p-4">
            <p class="text-gold-500 font-semibold text-xs mb-1">Saldo Awal</p>
            <p class="text-xl font-bold text-navy-800">{{ number_format($saldoAwal) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-4">
            <p class="text-emerald-600 font-semibold text-xs mb-1">Masuk</p>
            <p class="text-xl font-bold text-emerald-700">+{{ number_format($masuk) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-4">
            <p class="text-rose-600 font-semibold text-xs mb-1">Keluar</p>
            <p class="text-xl font-bold text-rose-700">−{{ number_format($keluar) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 font-medium">Tipe</th>
                        <th class="px-4 py-3 font-medium">Alasan</th>
                        <th class="px-4 py-3 font-medium text-right">Masuk</th>
                        <th class="px-4 py-3 font-medium text-right">Keluar</th>
                        <th class="px-4 py-3 font-medium text-right">Saldo</th>
                        <th class="px-4 py-3 font-medium">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    <tr class="bg-navy-50/40">
                        <td class="px-4 py-3 text-navy-400 italic">— saldo awal —</td>
                        <td></td><td></td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-navy-800">{{ number_format($saldoAwal) }}</td>
                        <td></td>
                    </tr>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-navy-500 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-navy-600 text-xs font-semibold">{{ $log->tipe }}</span>
                            </td>
                            <td class="px-4 py-3 text-navy-600">{{ $log->alasan }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600 font-mono">{{ $log->perubahan > 0 ? '+' . number_format($log->perubahan) : '' }}</td>
                            <td class="px-4 py-3 text-right text-rose-600 font-mono">{{ $log->perubahan < 0 ? '−' . number_format(abs($log->perubahan)) : '' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-navy-800">{{ number_format($log->stok_sesudah) }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $log->pelaku }}</td>
                        </tr>
                    @empty
                        <x-empty-state colspan="7" icon="activity" message="Belum ada mutasi stok untuk produk ini di periode tersebut." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection