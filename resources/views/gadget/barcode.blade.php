@extends('layouts.app')

@section('title', 'Barcode ' . $gadget->nama_produk)

@section('content')

    <div class="flex items-center justify-between mb-6 print-hidden">
        <h1 class="text-xl font-bold text-navy-800">Label Barcode</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('gadget.show', $gadget->id) }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Cetak
            </button>
        </div>
    </div>

    <div class="max-w-md mx-auto bg-white rounded-2xl border border-navy-100 p-8 text-center">
        <h2 class="text-lg font-black text-navy-800">{{ $gadget->nama_produk }}</h2>
        <p class="text-sm text-navy-500 mt-1">Kategori: {{ $gadget->kategori }} — Stok: {{ $gadget->stock }} {{ $gadget->satuan }}</p>

        <div class="mt-6 bg-white rounded-xl border border-dashed border-navy-200 p-6">
            {!! $barcode !!}
        </div>

        <p class="text-xs text-navy-400 mt-4">Cetak label ini untuk ditempel pada rak atau produk.</p>
    </div>

    <style>
        @media print {
            .print-hidden {
                display: none !important;
            }

            body {
                background: white !important;
            }

            main {
                max-width: none !important;
            }
        }
    </style>

@endsection