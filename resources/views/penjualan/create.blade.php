@extends('layouts.app')

@section('title', 'Catat Penjualan')

@section('content')

    <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <h1 class="text-xl font-bold text-navy-800">Catat Transaksi Penjualan</h1>
        <p class="text-sm text-navy-400 mt-1 mb-6">Stok akan berkurang otomatis sesuai item.</p>

        <form action="{{ route('penjualan.store') }}" method="POST" id="formPenjualan" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Customer</label>
                    <input type="text" name="customer" value="{{ old('customer') }}" placeholder="Nama pembeli"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 mb-2">
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="No. HP (untuk struk WA)"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <div class="rounded-xl border border-gold-200 bg-gold-50 px-4 py-3">
                <label class="block text-sm font-medium text-gold-700 mb-1">Scan / Ketik SKU <span class="text-xs font-normal text-navy-400">(lalu tekan Enter)</span></label>
                <input type="text" id="scanSku" placeholder="misal: SKU-001" autocomplete="off"
                       class="w-full rounded-lg border border-gold-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            @error('items') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror

            <div id="itemsRow" class="space-y-3"></div>
            <input type="hidden" id="itemsTotal" name="items_total" value="0">

            <div class="flex flex-wrap items-end justify-between gap-4">
                <button type="button" onclick="tambahBaris()"
                        class="border border-navy-100 hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
                    + Tambah Baris Item
                </button>

                <div class="grid grid-cols-2 gap-3 w-full md:w-80">
                    <div>
                        <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Diskon (Rp)</label>
                        <input type="number" name="diskon" id="diskonInput" min="0" step="0.01" value="{{ old('diskon', 0) }}"
                               class="w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">PPN (%)</label>
                        <input type="number" name="pajak" id="pajakInput" min="0" max="100" step="0.5" value="{{ old('pajak', 0) }}"
                               class="w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide">Subtotal</div>
                    <div id="subtotalDisplay" class="text-lg font-bold text-navy-700">Rp 0</div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">PPN</div>
                    <div id="pajakDisplay" class="text-sm text-navy-600">Rp 0</div>
                    <div class="text-xs font-medium text-navy-400 uppercase tracking-wide mt-1">Total</div>
                    <div id="totalDisplay" class="text-2xl font-black text-gold-600">Rp 0</div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-gold-500 hover:bg-gold-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan Penjualan
                </button>
                <a href="{{ route('penjualan.index') }}"
                   class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        const products = @json($products);
        const hargaById = {};
        const produkBySku = {};
        products.forEach(p => {
            hargaById[p.id] = { beli: parseFloat(p.harga_beli) || 0, jual: parseFloat(p.harga_jual) || 0 };
            if (p.sku) produkBySku[String(p.sku).toLowerCase()] = p;
        });

        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

        function ambilProduk(id) {
            return products.find(p => String(p.id) === String(id));
        }

        function tambahBaris(produkId = '') {
            const wrap = document.getElementById('itemsRow');
            const idx = wrap.children.length;
            const div = document.createElement('div');
            div.className = 'grid grid-cols-12 gap-2 items-end';
            div.dataset.idx = idx;
            div.innerHTML = `
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Produk</label>
                    <select name="items[${idx}][gadget_id]" required class="row-produk w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        <option value="">— Pilih produk —</option>
                        ${products.map(p => `<option value="${p.id}" ${p.stock <= 0 ? 'disabled' : ''}>${p.nama_produk} (${p.sku ? p.sku : 'ID' + p.id})${p.stock <= 0 ? ' — STOK HABIS' : ' — stok ' + p.stock}</option>`).join('')}
                    </select>
                </div>
                <div class="col-span-3 md:col-span-2">
                    <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Harga Jual</label>
                    <input type="number" name="items[${idx}][harga_jual]" min="0" step="0.01" required class="row-harga w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="col-span-3 md:col-span-2">
                    <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Qty</label>
                    <input type="number" name="items[${idx}][qty]" min="1" value="1" required class="row-qty w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="col-span-4 md:col-span-2">
                    <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Subtotal</label>
                    <div class="row-subtotal text-sm font-bold text-navy-800 py-2.5">Rp 0</div>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <button type="button" onclick="hapusBaris(this)"
                            class="w-full text-sm text-rose-500 hover:bg-rose-50 rounded-lg px-2 py-2.5 transition-colors">Hapus</button>
                </div>`;
            wrap.appendChild(div);
            if (produkId) {
                div.querySelector('.row-produk').value = String(produkId);
                const p = ambilProduk(produkId);
                if (p) div.querySelector('.row-harga').value = parseFloat(p.harga_jual) || parseFloat(p.harga_beli) || 0;
            }
            hitung();
            return div;
        }

        function hapusBaris(btn) {
            btn.closest('div[data-idx]').remove();
            renomori();
            hitung();
        }

        function renomori() {
            document.querySelectorAll('#itemsRow div[data-idx]').forEach((row, i) => {
                row.dataset.idx = i;
                row.querySelectorAll('input,select').forEach(el => {
                    const n = el.name.replace(/\d+/, i);
                    el.name = n;
                });
            });
        }

        function scanProduk(e) {
            const inp = document.getElementById('scanSku');
            const nilai = inp.value.trim();
            if (!nilai) return;
            const p = produkBySku[nilai.toLowerCase()];
            if (!p) {
                alert('SKU tidak ditemukan: ' + nilai);
                inp.value = '';
                inp.focus();
                return;
            }
            if (p.stock <= 0) {
                alert('Stok "' + p.nama_produk + '" habis.');
                inp.value = '';
                inp.focus();
                return;
            }
            let baris = null;
            document.querySelectorAll('#itemsRow div[data-idx]').forEach(r => {
                if (!baris && String(r.querySelector('.row-produk').value) === String(p.id)) {
                    const qty = r.querySelector('.row-qty');
                    const stok = parseInt(p.stock, 10);
                    if (parseInt(qty.value, 10) < stok) {
                        qty.value = parseInt(qty.value, 10) + 1;
                        baris = r;
                    }
                }
            });
            if (!baris) tambahBaris(p.id);
            inp.value = '';
            inp.focus();
            hitung();
        }

        function hitung() {
            let total = 0;
            document.querySelectorAll('#itemsRow div[data-idx]').forEach(row => {
                const select = row.querySelector('.row-produk');
                const harga = row.querySelector('.row-harga');
                const qty = row.querySelector('.row-qty');
                const sub = row.querySelector('.row-subtotal');
                const id = parseInt(select.value, 10);
                if (id && hargaById[id]) {
                    if (harga.value === '' || parseFloat(harga.value) === 0) harga.value = hargaById[id].jual || hargaById[id].beli;
                }
                const s = (parseFloat(harga.value) || 0) * (parseInt(qty.value) || 0);
                sub.textContent = fmt(s);
                total += s;
            });
            const diskon = parseFloat(document.getElementById('diskonInput').value) || 0;
            const ppn = parseFloat(document.getElementById('pajakInput').value) || 0;
            const dasar = Math.max(0, total - diskon);
            const pajakNom = dasar * ppn / 100;
            const grand = dasar + pajakNom;
            document.getElementById('subtotalDisplay').textContent = fmt(total);
            document.getElementById('pajakDisplay').textContent = fmt(pajakNom);
            document.getElementById('totalDisplay').textContent = fmt(grand);
            document.getElementById('itemsTotal').value = grand.toFixed(2);
        }

        document.addEventListener('input', e => {
            if (['row-produk', 'row-harga', 'row-qty'].some(c => e.target.classList.contains(c))) hitung();
        });
        document.getElementById('diskonInput').addEventListener('input', hitung);
        document.getElementById('pajakInput').addEventListener('input', hitung);
        document.getElementById('scanSku').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); scanProduk(e); } });
        tambahBaris();
    </script>
@endpush