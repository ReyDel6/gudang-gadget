@extends('layouts.app')

@section('title', 'Shift Kasir')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Shift Kasir</h1>
        <a href="{{ route('pos.index') }}"
           class="bg-gold-500 hover:bg-gold-600 text-navy-900 text-sm font-bold px-4 py-2 rounded-lg transition-colors shadow-lg shadow-gold-500/30">
            Buka Layar Kasir
        </a>
    </div>

    @if ($shiftAktif)
        <div class="bg-white rounded-2xl border border-emerald-200 p-6 mb-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        <h2 class="font-black text-navy-800">Shift #{{ $shiftAktif->id }} sedang berjalan</h2>
                    </div>
                    <p class="text-sm text-navy-500 mt-1">
                        Buka: {{ $shiftAktif->opened_at->format('d M Y H:i') }}
                        @if ($shiftAktif->notes)
                            <span class="block mt-0.5">Catatan: {{ $shiftAktif->notes }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium text-navy-400 uppercase tracking-wide">Modal Awal</p>
                    <p class="text-lg font-bold text-navy-800">Rp {{ number_format($shiftAktif->start_cash, 0, ',', '.') }}</p>
                    <p class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-2">Pendapatan (transaksi aktif)</p>
                    <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($shiftAktif->omzet, 0, ',', '.') }}
                        <span class="text-xs text-navy-400 font-semibold">· {{ $shiftAktif->jumlah_transaksi }} trx</span></p>
                </div>
            </div>

            <form action="{{ route('shift.tutup', $shiftAktif->id) }}" method="POST"
                  class="mt-5 pt-5 border-t border-navy-100 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">Uang Tunai Fisik Saat Tutup *</label>
                    <input type="number" name="actual_cash" min="0" step="0.01" required
                           class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">Catatan (opsional)</label>
                    <input type="text" name="notes" maxlength="255"
                           class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <button type="submit" onclick="return confirm('Tutup shift #{{ $shiftAktif->id }}?')"
                        class="bg-navy-800 hover:bg-navy-900 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
                    Tutup Shift
                </button>
            </form>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-navy-100 p-6 mb-6">
            <h2 class="font-black text-navy-800 mb-1">Buka Shift Baru</h2>
            <p class="text-sm text-navy-500 mb-4">Mulai bertugas sebagai kasir. Transaksi yang dicatat akan tertaut ke shift ini.</p>
            <form action="{{ route('shift.buka') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end max-w-2xl">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">Modal Awal Kas (Rp)</label>
                    <input type="number" name="start_cash" min="0" step="0.01" value="0"
                           class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">Catatan (opsional)</label>
                    <input type="text" name="notes" maxlength="255"
                           class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-gold-500 hover:bg-gold-600 text-navy-900 font-bold px-6 py-2.5 rounded-lg transition-colors shadow-lg shadow-gold-500/30">
                        Buka Shift
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Shift</th>
                        <th class="px-4 py-3 font-medium">Dibuka</th>
                        <th class="px-4 py-3 font-medium">Ditutup</th>
                        <th class="px-4 py-3 font-medium text-right">Modal</th>
                        <th class="px-4 py-3 font-medium text-right">Omzet</th>
                        <th class="px-4 py-3 font-medium text-right">Trx</th>
                        <th class="px-4 py-3 font-medium text-right">Uang Akhir</th>
                        <th class="px-4 py-3 font-medium text-right">Uang Fisik</th>
                        <th class="px-4 py-3 font-medium text-right">Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($shifts as $s)
                        @php
                            $selisih = $s->difference !== null ? (float) $s->difference : null;
                        @endphp
                        <tr class="{{ $s->is_open ? 'bg-emerald-50/50' : '' }}">
                            <td class="px-4 py-3 font-mono font-bold text-navy-800">#{{ $s->id }}
                                @if ($s->is_open)
                                    <span class="ml-1 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded px-1.5 py-0.5">AKTIF</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-navy-600">{{ $s->opened_at->format('d M Y · H:i') }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $s->closed_at?->format('d M Y · H:i') ?: '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-navy-700">Rp {{ number_format($s->start_cash, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-navy-800">Rp {{ number_format($s->omzet, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-navy-700">{{ $s->jumlah_transaksi }}</td>
                            <td class="px-4 py-3 text-right font-mono text-navy-700">{{ $s->end_cash !== null ? 'Rp ' . number_format($s->end_cash, 0, ',', '.') : '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-navy-700">{{ $s->actual_cash !== null ? 'Rp ' . number_format($s->actual_cash, 0, ',', '.') : '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold {{ $selisih === null ? 'text-navy-300' : ($selisih < 0 ? 'text-rose-600' : 'text-emerald-600') }}">
                                {{ $selisih === null ? '—' : number_format($selisih, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="9" icon="activity" message="Belum ada riwayat shift." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection