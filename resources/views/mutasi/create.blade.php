@extends('layouts.app')

@section('title', 'Catat Mutasi Stok')

@section('content')

    <div class="max-w-2xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <h1 class="text-xl font-bold text-navy-800">Catat Mutasi Stok</h1>
        <p class="text-sm text-navy-400 mt-1 mb-6">Pencatatan penerimaan, pengeluaran, retur, atau penyesuaian stok.</p>

        <form action="{{ route('mutasi.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Produk</label>
                <select name="gadget_id" required
                        class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('gadget_id') ? 'border-rose-400' : 'border-navy-100' }}">
                    <option value="">— Pilih Produk —</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(old('gadget_id') == $product->id)>
                            {{ $product->nama_produk }} (SKU {{ $product->sku ?: '—' }}, stok {{ $product->stock }} {{ $product->satuan }})
                        </option>
                    @endforeach
                </select>
                @error('gadget_id') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Jenis Mutasi</label>
                    <select name="jenis" id="jenis-mutasi" required
                            class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('jenis') ? 'border-rose-400' : 'border-navy-100' }}">
                        @foreach (['Penerimaan', 'Pengeluaran', 'Retur', 'Penyesuaian (+)', 'Penyesuaian (-)'] as $jenis)
                            <option value="{{ $jenis }}" @selected(old('jenis') == $jenis)>{{ $jenis }}</option>
                        @endforeach
                    </select>
                    @error('jenis') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Jumlah</label>
                    <input type="number" name="qty" required min="1" value="{{ old('qty') }}" placeholder="cth: 5"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('qty') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('qty') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Alasan / Keterangan</label>
                <input type="text" name="alasan" required value="{{ old('alasan') }}" placeholder="cth: Retur rusak dari customer"
                       class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('alasan') ? 'border-rose-400' : 'border-navy-100' }}">
                @error('alasan') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan Mutasi
                </button>
                <a href="{{ route('mutasi.index') }}"
                   class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection