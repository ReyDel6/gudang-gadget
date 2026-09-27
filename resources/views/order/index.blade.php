@extends('layouts.app')

@section('title', 'Order Publik')

@php
    $statusAktif = $status ?? null;
    $tabs = [
        '' => ['label' => 'Semua'],
        'pending' => ['label' => 'Menunggu', 'count' => $counts['pending']],
        'confirmed' => ['label' => 'Terkonfirmasi', 'count' => $counts['confirmed']],
        'cancelled' => ['label' => 'Dibatalkan', 'count' => $counts['cancelled']],
    ];
@endphp

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Order Publik (Checkout Storefront)</h1>
        <span class="text-sm text-navy-400">Pesanan masuk dari katalog publik &amp; portal mitra.</span>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">{{ session('error') }}</div>
    @endif

    @php $tabs = collect($tabs); @endphp
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach ($tabs as $tabStatus => $tab)
            <a href="{{ route('order.index', ['status' => $tabStatus ?: null, 'q' => $cari ?? '']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors
                      {{ ($statusAktif ?: '') === $tabStatus ? 'bg-navy-800 text-white' : 'bg-white text-navy-600 hover:bg-navy-50' }}">
                {{ $tab['label'] }}
                @if (!empty($tab['count']))
                    <span class="text-[10px] font-black px-1.5 py-0.5 rounded-full {{ ($statusAktif ?: '') === $tabStatus ? 'bg-gold-500 text-navy-900' : 'bg-navy-100 text-navy-500' }}">{{ $tab['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('order.index') }}" class="mb-5 flex items-center gap-2 max-w-md">
        <input type="text" name="q" value="{{ $cari ?? '' }}" placeholder="Cari kode / nama / telepon..."
               class="flex-1 rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        <button type="submit" class="bg-navy-800 hover:bg-navy-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">Cari</button>
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-navy-400 border-b border-navy-100 bg-navy-50/60">
                        <th class="px-4 py-3 font-bold">Kode</th>
                        <th class="px-4 py-3 font-bold">Pelanggan</th>
                        <th class="px-4 py-3 font-bold">Metode</th>
                        <th class="px-4 py-3 font-bold">Item</th>
                        <th class="px-4 py-3 font-bold text-right">Subtotal</th>
                        <th class="px-4 py-3 font-bold">Status</th>
                        <th class="px-4 py-3 font-bold text-right">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-50">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-navy-50/40 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('order.show', $order->id) }}" class="font-bold text-navy-800 hover:text-gold-600">{{ $order->kode }}</a>
                                @if ($order->reseller_id)
                                    <p class="text-[10px] font-bold text-gold-600 uppercase mt-0.5">Via Mitra</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-navy-800">{{ $order->nama_pelanggan }}</p>
                                <p class="text-xs text-navy-400">{{ $order->telepon }}</p>
                            </td>
                            <td class="px-4 py-3 text-navy-500">{{ $order->payment_label }}</td>
                            <td class="px-4 py-3 text-navy-500">{{ $order->items_count }}</td>
                            <td class="px-4 py-3 text-right font-bold text-navy-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $warna = match ($order->status) {
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'confirmed' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-rose-100 text-rose-600',
                                    };
                                @endphp
                                <span class="inline-flex text-[11px] font-black px-2 py-1 rounded-full {{ $warna }}">{{ $order->status_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-navy-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-navy-400">
                                Belum ada order publik.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-navy-100">
            {{ $orders->links() }}
        </div>
    </div>

@endsection