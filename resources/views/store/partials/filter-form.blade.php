@php
    $kategoriList = $categories ?? collect();
    $brandList = $brands ?? collect();
    $includeSort = $includeSort ?? false;
@endphp

<form method="GET" action="{{ route('shop.katalog') }}" class="space-y-4">
    @if (request('q'))
        <input type="hidden" name="q" value="{{ request('q') }}">
    @endif
    @if ($includeSort && request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif

    @if ($includeSort)
        <div>
            <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Urutkan</label>
            <select name="sort" onchange="this.form.submit()"
                    class="mt-1.5 w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <option value="">Rekomendasi</option>
                <option value="termurah" @selected(request('sort') === 'termurah')>Harga Terendah</option>
                <option value="termahal" @selected(request('sort') === 'termahal')>Harga Tertinggi</option>
                <option value="terbaru" @selected(request('sort') === 'terbaru')>Produk Terbaru</option>
                <option value="populer" @selected(request('sort') === 'populer')>Paling Populer</option>
            </select>
        </div>
    @endif

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Kategori</label>
        <select name="kategori" onchange="this.form.submit()"
                class="mt-1.5 w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            <option value="">Semua Kategori</option>
            @foreach ($kategoriList as $kategori)
                <option value="{{ $kategori }}" @selected(request('kategori') === $kategori)>{{ $kategori }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Brand</label>
        <select name="brand" onchange="this.form.submit()"
                class="mt-1.5 w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            <option value="">Semua Brand</option>
            @foreach ($brandList as $brand)
                <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Rentang Harga (Rp)</label>
        <div class="mt-1.5 flex items-center gap-2">
            <input type="number" name="min" value="{{ request('min') }}" placeholder="Min"
                   class="w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            <input type="number" name="max" value="{{ request('max') }}" placeholder="Maks"
                   class="w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        </div>
    </div>

    <div>
        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Kondisi</label>
        <select name="condition" onchange="this.form.submit()"
                class="mt-1.5 w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            <option value="">Semua Kondisi</option>
            <option value="new" @selected(request('condition') === 'new')>Baru / Segel</option>
            <option value="like-new" @selected(request('condition') === 'like-new')>Bekas Mulus</option>
            <option value="used" @selected(request('condition') === 'used')>Second</option>
        </select>
    </div>

    <label class="flex items-center gap-2 text-sm text-navy-700 cursor-pointer">
        <input type="checkbox" name="ready" value="1" @checked(request('ready'))
               onchange="this.form.submit()" class="h-4 w-4 rounded border-navy-200 text-gold-500 focus:ring-gold-500">
        Hanya Ready Stock
    </label>

    <button type="submit" class="w-full bg-navy-900 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">Terapkan Filter</button>
</form>