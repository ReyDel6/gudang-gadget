@extends('layouts.app')

@section('title', 'Tiket Servis & Reparasi')

@php
    $statusAktif = $status;
    $tabs = collect([['kode' => '', 'label' => 'Semua', 'count' => $ringkasan->sum()]]);
    foreach (\App\Models\ServiceTicket::STATUS as $kode => $label) {
        $tabs->push(['kode' => $kode, 'label' => $label, 'count' => $ringkasan[$kode] ?? 0]);
    }
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Tiket Servis &amp; Reparasi</h1>
            <p class="text-sm text-navy-400">Manajemen work order servis gadget &amp; pemakaian suku cadang gudang.</p>
        </div>
        <a href="{{ route('servis.create') }}"
           class="inline-flex items-center gap-2 bg-gold-500 hover:bg-gold-600 text-navy-900 text-sm font-black px-5 py-2.5 rounded-xl transition-colors">
            + Tiket Servis Baru
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach ($tabs as $tab)
            <a href="{{ route('servis.index', ['status' => $tab['kode'] ?: null, 'q' => $q]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors
                      {{ ($statusAktif ?: '') === $tab['kode'] ? 'bg-navy-800 text-white' : 'bg-white text-navy-600 hover:bg-navy-50' }}">
                {{ $tab['label'] }}
                @if ($tab['count'] > 0)
                    <span class="text-[10px] font-black px-1.5 py-0.5 rounded-full {{ ($statusAktif ?: '') === $tab['kode'] ? 'bg-gold-500 text-navy-900' : 'bg-navy-100 text-navy-500' }}">{{ $tab['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('servis.index') }}" class="mb-5 flex items-center gap-2 max-w-md">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari no. tiket / pelanggan / model / IMEI..."
               class="flex-1 rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        <button type="submit" class="bg-navy-800 hover:bg-navy-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">Cari</button>
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-navy-400 border-b border-navy-100 bg-navy-50/60">
                        <th class="px-4 py-3 font-bold">No. Tiket</th>
                        <th class="px-4 py-3 font-bold">Pelanggan</th>
                        <th class="px-4 py-3 font-bold">Unit</th>
                        <th class="px-4 py-3 font-bold">Teknisi</th>
                        <th class="px-4 py-3 font-bold text-center">Item</th>
                        <th class="px-4 py-3 font-bold text-right">Total</th>
                        <th class="px-4 py-3 font-bold text-right">Sisa</th>
                        <th class="px-4 py-3 font-bold">Status</th>
                        <th class="px-4 py-3 font-bold text-right">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-50">
                    @forelse ($tikets as $tiket)
                        <tr class="hover:bg-navy-50/40 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('servis.show', $tiket->id) }}" class="font-bold text-navy-800 hover:text-gold-600">{{ $tiket->no_tiket }}</a>
                                @if ($tiket->is_garansi)
                                    <p class="text-[10px] font-bold text-gold-600 uppercase mt-0.5">Klaim Garansi</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-navy-800">{{ $tiket->customer_name }}</p>
                                <p class="text-xs text-navy-400">{{ $tiket->customer_phone }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-navy-700">{{ $tiket->device_model }}</p>
                                <p class="text-xs text-navy-400">{{ $tiket->device_brand }}@if ($tiket->imei_or_serial) · {{ $tiket->imei_or_serial }}@endif</p>
                            </td>
                            <td class="px-4 py-3 text-navy-500">{{ $tiket->technician?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-navy-500">{{ $tiket->items_count }}</td>
                            <td class="px-4 py-3 text-right font-bold text-navy-900">Rp {{ number_format((float) $tiket->total_cost, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $tiket->remaining_cost > 0 ? 'text-gold-600' : 'text-emerald-600' }}">
                                Rp {{ number_format((float) $tiket->remaining_cost, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex text-[11px] font-black px-2 py-1 rounded-full {{ $tiket->status_warna }}">{{ $tiket->status_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-navy-400">{{ $tiket->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-navy-400">
                                Belum ada tiket servis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-navy-100">
            {{ $tikets->links() }}
        </div>
    </div>

@endsection