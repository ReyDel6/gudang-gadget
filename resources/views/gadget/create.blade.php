@extends('layouts.app')

@section('title', 'Formulir Tambah Produk')

@section('content')

    <div class="max-w-3xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <h1 class="text-xl font-bold text-navy-800">Formulir Tambah Produk</h1>
        <p class="text-sm text-navy-400 mt-1 mb-6">Kolom bertanda <span class="text-rose-500">*</span> wajib diisi.</p>

        <form action="{{ route('gadget.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_produk" required value="{{ old('nama_produk') }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('nama_produk') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('nama_produk') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Kosongkan untuk otomatis"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('sku') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('sku') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="kategori" list="kategori-list" required value="{{ old('kategori') }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('kategori') ? 'border-rose-400' : 'border-navy-100' }}">
                    <datalist id="kategori-list">
                        @foreach ($kategoriList as $kategori)
                            <option value="{{ $kategori }}"></option>
                        @endforeach
                    </datalist>
                    @error('kategori') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" list="supplier-list" value="{{ old('supplier') }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <datalist id="supplier-list">
                        @foreach ($supplierList as $supplier)
                            <option value="{{ $supplier }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak') }}" placeholder="cth: RAK-A1"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Stok <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" required min="0" value="{{ old('stock', 0) }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('stock') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('stock') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Stok Minimum</label>
                    <input type="number" name="stok_minimum" min="0" value="{{ old('stok_minimum') }}" placeholder="cth: 3"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" value="{{ old('satuan', 'pcs') }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" min="0" step="0.01" value="{{ old('harga_beli', 0) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" min="0" step="0.01" value="{{ old('harga_jual', 0) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Harga Promo / Coret (Rp)</label>
                    <input type="number" name="harga_promo" min="0" step="0.01" value="{{ old('harga_promo') }}" placeholder="cth: 10000000"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <p class="text-xs text-navy-400 mt-1">Kosongkan jika tanpa promo.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" value="{{ old('tanggal_pembelian') }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Status</label>
                    <select name="status" required
                            class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                        @foreach (['Tersedia', 'Habis', 'Tidak Dijual'] as $status)
                            <option value="{{ $status }}" @selected(old('status', 'Tersedia') == $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Foto Produk</label>
                    <input type="file" name="foto" accept="image/*"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @error('foto') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-gold-50 border border-gold-200 rounded-xl p-5 space-y-4">
                <h2 class="font-black text-navy-800 text-sm uppercase tracking-wide text-gold-700">Storefront</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Kondisi Barang</label>
                        <select name="condition"
                                class="w-full rounded-lg border border-gold-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                            @foreach ([['new', 'Baru / Segel'], ['like-new', 'Bekas Mulus'], ['used', 'Second']] as [$val, $label])
                                <option value="{{ $val }}" @selected(old('condition', 'new') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-navy-700 mb-1">Informasi Garansi (opsional)</label>
                        <input type="text" name="warranty_info" maxlength="255" value="{{ old('warranty_info') }}"
                               placeholder="cth: Garansi toko 7 hari"
                               class="w-full rounded-lg border border-gold-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 text-sm font-medium text-navy-700 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', true)) class="h-4 w-4 rounded border-navy-200 text-gold-500">
                        Tampilkan di Katalog Publik
                    </label>
                    <label class="flex items-center gap-2 text-sm font-medium text-navy-700 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured')) class="h-4 w-4 rounded border-navy-200 text-gold-500">
                        Jadikan Produk Unggulan (vitrin beranda)
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('deskripsi') ? 'border-rose-400' : 'border-navy-100' }}">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan
                </button>
                <a href="{{ route('gadget.index') }}"
                   class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection