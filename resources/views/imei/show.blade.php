@extends('layouts.app')

@section('title', 'Riwayat IMEI ' . $imei->imei)

@section('content')

    <div class="flex items-center justify-between mb-6 print-hidden">
        <div>
            <a href="{{ route('imei.index') }}" class="text-xs font-semibold text-navy-400 hover:text-gold-600">← Kembali ke daftar IMEI</a>
            <h1 class="text-xl font-bold text-navy-800 mt-1">Riwayat Unit {{ $imei->imei }}</h1>
        </div>
        <button onclick="window.print()"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            Cetak Riwayat
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-navy-400">Produk</p>
            <p class="font-bold text-navy-800 mt-1">{{ $imei->gadget?->nama_produk }}</p>
            <p class="text-sm text-navy-500 mt-0.5">{{ $imei->gadget?->sku }} · {{ $imei->color ?: 'warna tidak tercatat' }}</p>
            <p class="mt-3 text-xs font-bold uppercase tracking-wide text-navy-400">Status Unit</p>
            <span class="inline-block mt-1 rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-bold">{{ $imei->status_label }}</span>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-navy-400">Riwayat Masuk</p>
            <p class="font-bold text-navy-800 mt-1">{{ $imei->masuk_at?->format('d M Y H:i') ?: '-' }}</p>
            <p class="text-sm text-navy-500 mt-0.5">{{ $imei->masuk_via ? 'via ' . $imei->masuk_via : '-' }}</p>
            <p class="mt-3 text-xs font-bold uppercase tracking-wide text-navy-400">Masa Garansi</p>
            <p class="font-bold text-navy-800 mt-1">
                @if ($imei->warranty_expired_at)
                    {{ $imei->warranty_expired_at->format('d M Y') }}
                    <span class="{{ $imei->warranty_expired_at->isPast() ? 'text-rose-500' : 'text-emerald-600' }} text-xs font-bold">
                        ({{ $imei->sisa_garansi }} hari)
                    </span>
                @else
                    <span class="text-navy-400 font-normal">Tidak tercatat</span>
                @endif
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-navy-400">Riwayat Penjualan</p>
            @if ($imei->penjualanItem?->penjualan)
                <p class="font-bold text-navy-800 mt-1">{{ $imei->penjualanItem->penjualan->no_invoice }}</p>
                <p class="text-sm text-navy-500 mt-0.5">
                    Terjual {{ $imei->sold_at?->format('d M Y H:i') }} ke
                    <b>{{ $imei->penjualanItem->penjualan->customer ?: 'Umum' }}</b>
                </p>
                <a href="{{ route('penjualan.show', $imei->penjualanItem->penjualan->id) }}"
                   class="inline-block mt-2 text-xs font-bold text-gold-600 hover:text-gold-700">Lihat nota penjualan →</a>
            @else
                <p class="text-navy-400 mt-1 text-sm">Belum terjual (masih di gudang).</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-navy-100">
            <h2 class="font-black text-navy-800">Riwayat Servis / Garansi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">No. Tiket</th>
                        <th class="px-4 py-3 font-medium">Keluhan</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Teknisi</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($servis as $tiket)
                        <tr>
                            <td class="px-4 py-3 font-mono font-semibold text-navy-800">{{ $tiket->no_tiket }}</td>
                            <td class="px-4 py-3 text-navy-600 max-w-[260px] truncate">{{ $tiket->problem_description }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-gold-100 text-gold-800 px-2.5 py-0.5 text-xs font-semibold">{{ $tiket->status_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-navy-600">{{ $tiket->teknisi_name ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('servis.show', $tiket->id) }}"
                                   class="text-gold-600 hover:text-gold-700 font-semibold text-xs">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-navy-400">Belum ada tiket servis untuk unit ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($imei->notes)
        <div class="bg-gold-50 border border-gold-200 rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-gold-700">Catatan</p>
            <p class="text-sm text-navy-700 mt-1">{{ $imei->notes }}</p>
        </div>
    @endif

@endsection