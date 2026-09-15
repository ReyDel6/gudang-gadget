@extends('layouts.app')

@section('title', $gadget->nama_produk)

@section('content')

    @php
        $badge = match ($gadget->status) {
            'Tersedia' => 'bg-emerald-50 text-emerald-600',
            'Habis' => 'bg-rose-50 text-rose-600',
            default => 'bg-slate-100 text-slate-500',
        };
    @endphp

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Detail Produk</h1>
        <div class="flex items-center gap-3 print-hidden">
            <a href="{{ route('gadget.index') }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Kembali
            </a>
            <a href="{{ route('gadget.edit', $gadget->id) }}"
               class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Foto --}}
        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <div class="aspect-square rounded-xl overflow-hidden bg-navy-50 border border-navy-100">
                @if ($gadget->thumbnail)
                    <img src="{{ $gadget->foto_url }}" alt="{{ $gadget->nama_produk }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full grid place-items-center text-navy-300 text-5xl">📦</div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="bg-white rounded-2xl border border-navy-100 p-6 md:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="text-xs font-medium text-navy-400 uppercase tracking-wide">ID #{{ $gadget->id }}</span>
                    <h2 class="text-2xl font-black text-navy-800 mt-1">{{ $gadget->nama_produk }}</h2>
                    <span class="mt-2 inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                        {{ $gadget->status }}
                    </span>
                    <span class="ml-2 inline-block rounded-full bg-navy-100 px-3 py-1 text-xs font-semibold text-navy-600">
                        {{ $gadget->kategori }}
                    </span>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-semibold text-navy-700 mb-1">Deskripsi</h3>
                <p class="text-navy-600 whitespace-pre-line">{{ $gadget->deskripsi }}</p>
            </div>

            <div class="mt-6 p-4 rounded-xl bg-navy-50/60 border border-navy-100 print-hidden">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Stok Saat Ini</div>
                        <div class="text-3xl font-black text-navy-800 mt-1">{{ $gadget->stock }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('gadget.stok', [$gadget->id, 'turun']) }}" method="POST">
                            @csrf
                            <button type="submit" title="Kurangi stok"
                                    class="w-11 h-11 grid place-items-center rounded-xl border border-navy-200 bg-white text-navy-700 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 text-xl font-bold transition-colors">−</button>
                        </form>
                        <form action="{{ route('gadget.stok', [$gadget->id, 'naik']) }}" method="POST">
                            @csrf
                            <button type="submit" title="Tambah stok"
                                    class="w-11 h-11 grid place-items-center rounded-xl border border-navy-200 bg-white text-navy-700 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 text-xl font-bold transition-colors">+</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection