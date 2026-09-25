@extends('layouts.app')

@section('title', 'Import Produk dari CSV')

@section('content')

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-navy-800">Import Produk via CSV</h1>
                <p class="text-sm text-navy-400 mt-1">Tambah banyak produk sekaligus.</p>
            </div>
            <a href="{{ route('gadget.import-template') }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Unduh Template
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-8">
            <form action="{{ route('gadget.import-store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">File CSV</label>
                    <input type="file" name="file" accept=".csv,.txt" required
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('file') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('file') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-xl bg-navy-50/60 border border-navy-100 p-4 text-sm text-navy-600 space-y-1">
                    <p class="font-semibold text-navy-700">Kolom yang didukung (header opsional):</p>
                    <p class="font-mono text-xs">sku, nama_produk*, kategori, deskripsi, stock, status, harga_beli, satuan, stok_minimum, supplier, lokasi_rak, serial_number, tanggal_pembelian</p>
                    <p class="text-xs text-navy-400">Hanya kolom <span class="font-semibold">nama_produk</span> yang wajib. Kategori default: "Umum". SKU diisi otomatis bila kosong.</p>
                </div>

                <button type="submit"
                        class="w-full bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Upload &amp; Import
                </button>
            </form>
        </div>
    </div>

@endsection