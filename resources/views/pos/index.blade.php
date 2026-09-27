<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Kasir — Gudang Gadget</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite('resources/css/app.css')
</head>
<body class="bg-navy-100/60 text-navy-900 h-screen overflow-hidden">

    <div class="h-full flex flex-col">

        {{-- HEADER --}}
        <header class="bg-navy-900 text-white px-4 py-3 flex items-center justify-between gap-4 shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gold-500 rounded-lg text-navy-900">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-black text-lg leading-tight">Layar <span class="text-gold-400">Kasir</span></h1>
                    <span class="text-xs text-slate-400">{{ date('d M Y, H:i') }} · {{ Auth::user()->name }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if ($shiftAktif)
                    <span class="text-xs bg-emerald-500/20 text-emerald-300 px-3 py-1.5 rounded-lg font-semibold">
                        Shift #{{ $shiftAktif->id }} aktif · modal Rp {{ number_format($shiftAktif->start_cash, 0, ',', '.') }}
                    </span>
                @else
                    <a href="{{ route('shift.index') }}"
                       class="text-xs bg-rose-500/20 text-rose-300 px-3 py-1.5 rounded-lg font-semibold hover:bg-rose-500/30">
                        Shift belum dibuka — Klik
                    </a>
                @endif
                <a href="{{ route('shift.index') }}"
                   class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg font-semibold">Shift</a>
                <a href="{{ route('laporan.harian') }}"
                   class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg font-semibold">Rekap Hari Ini</a>
                <a href="{{ route('landing') }}"
                   class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg font-semibold">Panel</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="text-xs bg-white/10 hover:bg-rose-500/30 px-3 py-1.5 rounded-lg font-semibold">Keluar</button>
                </form>
            </div>
        </header>

        @if (session('success'))
            <div class="bg-emerald-500/15 text-emerald-800 px-4 py-2 text-sm font-semibold shrink-0">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-rose-500/15 text-rose-700 px-4 py-2 text-sm font-semibold shrink-0">
                <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- BODY --}}
        <div class="flex-1 flex min-h-0">

            {{-- KATALOG --}}
            <div class="flex-1 flex flex-col min-w-0 p-4 gap-3">

                <div class="flex gap-2">
                    <div class="flex-1 relative">
                        <input id="cariInput" type="text" placeholder="Cari produk / sku / seri..."
                               class="w-full rounded-xl border border-navy-200 bg-white pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 shadow-sm">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-navy-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3"/></svg>
                    </div>
                    <div class="relative">
                        <input id="scanInput" type="text" placeholder="Scan SKU/IMEI..." autocomplete="off"
                               class="w-56 rounded-xl border border-gold-400 bg-gold-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 shadow-sm">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-navy-400 tracking-wider rounded bg-navy-100 px-1.5 py-0.5">F2</span>
                    </div>
                </div>

                <div id="kategoriChips" class="flex gap-2 flex-wrap shrink-0">
                    <button data-kategori="" class="chip px-3.5 py-1.5 rounded-full text-xs font-bold bg-navy-800 text-white">Semua</button>
                    @foreach ($products->pluck('kategori')->filter()->unique()->sort() as $kat)
                        <button data-kategori="{{ $kat }}" class="chip px-3.5 py-1.5 rounded-full text-xs font-bold bg-white text-navy-600 border border-navy-200 hover:border-gold-400">{{ $kat }}</button>
                    @endforeach
                </div>

                <div id="produkGrid" class="flex-1 overflow-y-auto grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 content-start"></div>
            </div>

            {{-- KERANJANG --}}
            <div class="w-[380px] shrink-0 bg-white border-l border-navy-200 flex flex-col min-h-0">
                <div class="px-4 py-3 border-b border-navy-100 flex items-center justify-between shrink-0">
                    <h2 class="font-black text-navy-800">Keranjang <span id="jmlItemBadge" class="text-xs bg-gold-500 text-navy-900 rounded-full px-2 py-0.5">0</span></h2>
                    <div class="flex gap-2">
                        <button onclick="simpanHold()" title="Simpan keranjang sementara (F4)"
                                class="text-xs font-semibold border border-navy-200 rounded-lg px-3 py-1.5 text-navy-600 hover:bg-navy-50">Hold</button>
                        <button onclick="muatHold()" title="Ambil keranjang yang di-hold"
                                class="text-xs font-semibold border border-navy-200 rounded-lg px-3 py-1.5 text-navy-600 hover:bg-navy-50">Ambil Hold</button>
                        <button onclick="bersihkanCart()" title="Kosongkan keranjang (ESC)"
                                class="text-xs font-semibold border border-rose-200 rounded-lg px-3 py-1.5 text-rose-600 hover:bg-rose-50">Reset</button>
                    </div>
                </div>

                <div id="holdList" class="hidden px-4 py-2 bg-gold-50 border-b border-gold-100 text-xs space-y-1 shrink-0"></div>

                <div id="cartItems" class="flex-1 overflow-y-auto px-4 py-2"></div>

                <div class="px-4 py-3 border-t border-navy-100 space-y-2 shrink-0">
                    <div class="flex justify-between text-sm">
                        <span class="text-navy-500">Subtotal</span>
                        <span id="subtotalText" class="font-bold text-navy-800">Rp 0</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-bold text-navy-400 uppercase tracking-wide">Diskon (Rp)</label>
                            <input id="diskonInput" type="number" min="0" value="0"
                                   class="w-full rounded-lg border border-navy-100 px-2.5 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-navy-400 uppercase tracking-wide">PPN (%)</label>
                            <input id="pajakInput" type="number" min="0" max="100" step="0.5" value="0"
                                   class="w-full rounded-lg border border-navy-100 px-2.5 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-semibold text-navy-700">Total</span>
                        <span id="totalText" class="text-2xl font-black text-gold-600">Rp 0</span>
                    </div>
                    <button id="btnBayar" onclick="bukaBayar()" disabled
                            class="w-full bg-gold-500 hover:bg-gold-600 disabled:opacity-40 disabled:cursor-not-allowed text-navy-900 font-black py-4 rounded-xl text-lg transition-colors shadow-lg shadow-gold-500/30">
                        BAYAR · <span>Rp 0</span>
                    </button>
                    <div class="text-center text-[10px] text-navy-400 font-semibold">F8 = Bayar · F4 = Hold · ESC = Reset</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PEMBAYARAN --}}
    <div id="modalBayar" class="hidden fixed inset-0 z-50 bg-navy-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
            <div class="bg-navy-900 text-white px-5 py-4 flex justify-between items-center">
                <h3 class="font-black">Pembayaran</h3>
                <button onclick="tutupModal()" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>
            <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs font-bold text-navy-400 uppercase">Nama Pembeli</label>
                        <input id="custName" type="text" class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm" placeholder="Umum">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-navy-400 uppercase">No. HP</label>
                        <input id="custPhone" type="text" class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm" placeholder="08xxxx">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-navy-400 uppercase block mb-1.5">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" data-metode="cash" class="metrode px-3 py-3 rounded-xl border-2 border-gold-500 bg-gold-50 text-navy-900 font-bold text-sm">Tunai</button>
                        <button type="button" data-metode="qris" class="metrode px-3 py-3 rounded-xl border-2 border-navy-200 text-navy-600 font-bold text-sm">QRIS</button>
                        <button type="button" data-metode="transfer" class="metrode px-3 py-3 rounded-xl border-2 border-navy-200 text-navy-600 font-bold text-sm">Transfer</button>
                        <button type="button" data-metode="debit" class="metrode px-3 py-3 rounded-xl border-2 border-navy-200 text-navy-600 font-bold text-sm">Debit/EDC</button>
                        <button type="button" data-metode="split" class="metrode px-3 py-3 rounded-xl border-2 border-navy-200 text-navy-600 font-bold text-sm">Split</button>
                    </div>
                </div>

                <div id="bayarPanel" class="space-y-3"></div>

                <div class="bg-navy-50 rounded-xl p-3 flex items-center justify-between">
                    <span class="text-sm font-semibold text-navy-700">Total Tagihan</span>
                    <span id="modalTotal" class="text-xl font-black text-navy-900">Rp 0</span>
                </div>
                <div id="kembalianRow" class="hidden bg-emerald-50 rounded-xl p-3 flex items-center justify-between">
                    <span class="text-sm font-semibold text-emerald-700">Kembalian</span>
                    <span id="modalKembalian" class="text-xl font-black text-emerald-700">Rp 0</span>
                </div>

                <form id="formBayar" method="POST" action="{{ route('pos.store') }}" class="pt-1">
                    @csrf
                    <div id="hiddenItems"></div>
                    <button type="submit" id="btnProses"
                            class="w-full bg-navy-900 hover:bg-navy-800 text-white font-black py-4 rounded-xl text-lg transition-colors">
                        Selesaikan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    @php
        $produkJson = $products->map(fn ($p) => [
            'id' => $p->id,
            'nama' => $p->nama_produk,
            'sku' => $p->sku,
            'kategori' => $p->kategori,
            'beli' => (float) $p->harga_beli,
            'jual' => (float) $p->harga_jual ?: (float) $p->harga_beli,
            'stok' => (int) $p->stock,
            'tiers' => $p->tierPrices
                ->sortBy('min_qty')
                ->values()
                ->map(fn ($t) => ['name' => $t->tier_name, 'min' => (int) $t->min_qty, 'price' => (float) $t->price]),
        ])->values();
    @endphp

    <script>
        const products = @json($produkJson);

        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
        const pela = {};
        products.forEach(p => { pela[String(p.id)] = p; });

        let cart = [];
        let diskon = 0;
        let pajak = 0;
        let metode = 'cash';
        let bayarTunai = '';
        let refBayar = '';
        let splitTunai = '';

        const $ = sel => document.querySelector(sel);
        const byId = id => document.getElementById(id);
        const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));

        // ---------- KATALOG ----------
        function tierInfo(p, qty) {
            let t = null;
            (p.tiers || []).forEach(x => { if (qty >= x.min) t = x; });
            return t;
        }
        function hargaEfektif(p, qty) {
            const t = tierInfo(p, qty);
            return t ? t.price : p.jual;
        }
        let qKata = '';
        let qKategori = '';
        function renderGrid() {
            const el = byId('produkGrid');
            const k = qKata.toLowerCase().trim();
            const list = products.filter(p => {
                const cocokKata = !k || p.nama.toLowerCase().includes(k) || (p.sku && p.sku.toLowerCase().includes(k));
                const cocokKat = !qKategori || p.kategori === qKategori;
                return cocokKata && cocokKat;
            });
            el.innerHTML = list.map(p => `
                <button onclick="tambahProduk(${p.id})" class="text-left rounded-xl border border-navy-200 bg-white p-3 hover:border-gold-400 hover:shadow-md transition-all ${p.stok <= 0 ? 'opacity-45 cursor-not-allowed' : ''}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="text-sm font-bold text-navy-800 leading-snug">${esc(p.nama)}</div>
                        <span class="shrink-0 text-[10px] font-bold rounded px-1.5 py-0.5 ${p.stok > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}">${p.stok > 0 ? 'Stok ' + p.stok : 'Habis'}</span>
                    </div>
                    <div class="mt-1 text-[11px] text-navy-400 font-semibold">${p.sku ? esc(p.sku) : ''}</div>
                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                        <div class="text-lg font-black text-gold-600">${fmt(p.jual)}</div>
                        ${(p.tiers && p.tiers.length) ? '<span class="text-[9px] font-bold bg-navy-800 text-gold-300 rounded-full px-2 py-0.5 uppercase">Grosir Tersedia</span>' : ''}
                    </div>
                </button>`).join('') || '<div class="col-span-full text-center text-navy-400 py-10 text-sm">Produk tidak ditemukan.</div>';
        }

        document.querySelectorAll('.chip').forEach(c => c.addEventListener('click', () => {
            qKategori = c.dataset.kategori;
            document.querySelectorAll('.chip').forEach(x => {
                x.classList.toggle('bg-navy-800', x === c);
                x.classList.toggle('text-white', x === c);
                x.classList.toggle('bg-white', x !== c);
                x.classList.toggle('text-navy-600', x !== c);
            });
            renderGrid();
        }));
        byId('cariInput').addEventListener('input', e => { qKata = e.target.value; renderGrid(); });

        // ---------- SCAN ----------
        function scan() {
            const v = byId('scanInput').value.trim();
            if (!v) return;
            const p = products.find(x => x.sku && String(x.sku).toLowerCase() === v.toLowerCase());
            if (!p) { alert('SKU tidak ditemukan: ' + v); }
            else { tambahProduk(p.id); }
            byId('scanInput').value = '';
            byId('scanInput').focus();
        }
        byId('scanInput').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); scan(); } });

        // ---------- KERANJANG ----------
        function tambahProduk(id) {
            const p = pela[String(id)];
            if (!p || p.stok <= 0) return;
            const ada = cart.find(c => c.id === String(id));
            if (ada) {
                if (ada.qty < p.stok) { ada.qty++; ada.subtotal = ada.jual * ada.qty; }
                else { alert('Stok "' + p.nama + '" hanya tersisa ' + ada.qty + '.'); }
            } else {
                cart.push({ id: String(id), nama: p.nama, jual: p.jual, stok: p.stok, qty: 1, subtotal: p.jual });
            }
            renderCart();
        }
        function ubahQty(i, d) {
            const c = cart[i];
            c.qty += d;
            if (c.qty <= 0) { cart.splice(i, 1); }
            else if (c.qty > c.stok) { c.qty = c.stok; alert('Stok "' + c.nama + '" hanya tersisa ' + c.stok + '.'); }
            if (cart[i]) {
                const p = pela[String(c.id)];
                cart[i].jual = hargaEfektif(p, cart[i].qty);
                cart[i].subtotal = cart[i].jual * cart[i].qty;
            }
            renderCart();
        }
        function hapusItem(i) { cart.splice(i, 1); renderCart(); }

        function hitung() {
            diskon = Math.max(0, parseFloat(byId('diskonInput').value) || 0);
            pajak = Math.max(0, parseFloat(byId('pajakInput').value) || 0);
            const sub = cart.reduce((a, c) => a + c.subtotal, 0);
            const dasar = Math.max(0, sub - diskon);
            const total = dasar + dasar * pajak / 100;
            return { sub, dasar, total };
        }

        function renderCart() {
            const { sub, total } = hitung();
            const jml = cart.reduce((a, c) => a + c.qty, 0);
            byId('jmlItemBadge').textContent = jml;
            byId('subtotalText').textContent = fmt(sub);
            byId('totalText').textContent = fmt(total);
            byId('btnBayar').disabled = cart.length === 0;
            byId('btnBayar').innerHTML = 'BAYAR · <span>' + fmt(total) + '</span>';

            byId('cartItems').innerHTML = cart.length
                ? cart.map((c, i) => {
                    const t = tierInfo(pela[String(c.id)], c.qty);
                    return `
                    <div class="flex items-center gap-2 py-2 border-b border-navy-50">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <div class="text-sm font-bold text-navy-800 truncate">${esc(c.nama)}</div>
                                ${t ? `<span class="shrink-0 text-[9px] font-bold bg-navy-800 text-gold-300 rounded px-1.5 py-0.5 uppercase">${esc(t.name)}</span>` : ''}
                            </div>
                            <div class="text-[11px] text-navy-400">${fmt(c.jual)} × ${c.qty}</div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button onclick="ubahQty(${i},-1)" class="w-7 h-7 rounded-lg border border-navy-200 text-navy-700 font-bold">−</button>
                            <span class="w-8 text-center font-mono font-bold">${c.qty}</span>
                            <button onclick="ubahQty(${i},1)" class="w-7 h-7 rounded-lg border border-navy-200 text-navy-700 font-bold">+</button>
                        </div>
                        <div class="w-20 text-right text-sm font-bold text-navy-800">${fmt(c.subtotal)}</div>
                        <button onclick="hapusItem(${i})" class="text-rose-500 hover:bg-rose-50 rounded-lg px-1.5 text-sm">✕</button>
                    </div>`;}).join('')
                : '<div class="text-center text-navy-400 text-sm py-10">Keranjang kosong.<br>Tekan produk untuk menambah.</div>';
        }

        byId('diskonInput').addEventListener('input', e => { diskon = Math.max(0, parseFloat(e.target.value) || 0); renderCart(); });
        byId('pajakInput').addEventListener('input', e => { pajak = Math.max(0, parseFloat(e.target.value) || 0); renderCart(); });

        // ---------- HOLD CART ----------
        function simpanHold() {
            const { sub, total } = hitung();
            if (cart.length === 0) return alert('Keranjang kosong.');
            let slot = null;
            for (let i = 1; i <= 9; i++) { if (!localStorage.getItem('pos_hold_' + i)) { slot = i; break; } }
            localStorage.setItem('pos_hold_' + (slot || 9), JSON.stringify({
                cart, diskon, pajak,
                nama: byId('custName') ? byId('custName').value : '',
                phone: byId('custPhone') ? byId('custPhone').value : '',
                total
            }));
            bersihkanCart();
            renderHold();
        }
        function renderHold() {
            const el = byId('holdList');
            const list = [];
            for (let i = 1; i <= 9; i++) {
                const raw = localStorage.getItem('pos_hold_' + i);
                if (raw) list.push({ i, data: JSON.parse(raw) });
            }
            if (list.length === 0) { el.classList.add('hidden'); el.innerHTML = ''; return; }
            el.classList.remove('hidden');
            el.innerHTML = list.map(h => `
                <div class="flex items-center justify-between gap-2 bg-white rounded-lg px-2 py-1 border border-gold-200">
                    <span class="font-bold text-gold-700">Hold ${h.i} · ${fmt(h.data.total)} · ${h.data.cart.length} item</span>
                    <span class="flex gap-1">
                        <button onclick="muatHold(${h.i})" class="text-navy-600 font-bold hover:text-gold-600">Muat</button>
                        <button onclick="hapusHold(${h.i})" class="text-rose-500 font-bold hover:text-rose-600">Hapus</button>
                    </span>
                </div>`).join('');
        }
        function muatHold(i) {
            let slot = null;
            if (i === undefined) {
                for (let s = 1; s <= 9; s++) { if (localStorage.getItem('pos_hold_' + s)) { slot = s; break; } }
                if (!slot) return alert('Tidak ada keranjang yang di-hold.');
            } else { slot = i; }
            const h = JSON.parse(localStorage.getItem('pos_hold_' + slot));
            cart = h.cart; byId('diskonInput').value = h.diskon; byId('pajakInput').value = h.pajak;
            localStorage.removeItem('pos_hold_' + slot);
            renderHold(); renderCart();
        }
        function hapusHold(i) { localStorage.removeItem('pos_hold_' + i); renderHold(); }
        function bersihkanCart() { cart = []; byId('diskonInput').value = 0; byId('pajakInput').value = 0; renderCart(); }

        // ---------- PEMBAYARAN ----------
        function bukaBayar() {
            if (cart.length === 0) return;
            const { total } = hitung();
            metode = 'cash'; bayarTunai = String(Math.ceil(total)); refBayar = ''; splitTunai = '';
            byId('modalTotal').textContent = fmt(total);
            document.querySelectorAll('.metrode').forEach(b => {
                const aktif = b.dataset.metode === 'cash';
                b.classList.toggle('border-gold-500', aktif);
                b.classList.toggle('bg-gold-50', aktif);
                b.classList.toggle('text-navy-900', aktif);
                b.classList.toggle('border-navy-200', !aktif);
                b.classList.toggle('text-navy-600', !aktif);
            });
            renderBayar();
            byId('modalBayar').classList.remove('hidden');
            byId('custName').focus();
        }
        function tutupModal() { byId('modalBayar').classList.add('hidden'); }
        function pilihMetode(m) {
            metode = m;
            document.querySelectorAll('.metrode').forEach(b => {
                const aktif = b.dataset.metode === m;
                b.classList.toggle('border-gold-500', aktif);
                b.classList.toggle('bg-gold-50', aktif);
                b.classList.toggle('text-navy-900', aktif);
                b.classList.toggle('border-navy-200', !aktif);
                b.classList.toggle('text-navy-600', !aktif);
            });
            renderBayar();
        }
        document.querySelectorAll('.metrode').forEach(b => b.addEventListener('click', () => pilihMetode(b.dataset.metode)));
        byId('bayarPanel').addEventListener('input', e => {
            if (e.target.id === 'bayarTunai') bayarTunai = e.target.value;
            if (e.target.id === 'refBayar') refBayar = e.target.value;
            if (e.target.id === 'splitTunai') splitTunai = e.target.value;
            hitungKembalian();
        });

        function renderBayar() {
            const { total } = hitung();
            const panel = byId('bayarPanel');
            let html = '';
            if (metode === 'cash') {
                html = tentangTunai('Uang Diterima', 'bayarTunai', bayarTunai);
            } else if (metode === 'split') {
                html = tentangTunai('Tunai (sebagian)', 'splitTunai', splitTunai)
                     + `<div><label class="text-xs font-bold text-navy-400 uppercase block mb-1">Ref Bayar Non-Tunai (QRIS/Transfer/Debit)</label>
                        <input id="refBayar" type="text" value="${esc(refBayar)}" class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm" placeholder="No referensi"></div>`;
            } else {
                const label = metode === 'qris' ? 'IRIS No Ref / Kode' : 'No. Referensi (EDC/Transfer)';
                html = `<div><label class="text-xs font-bold text-navy-400 uppercase block mb-1">${label}</label>
                        <input id="refBayar" type="text" value="${esc(refBayar)}" class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm" placeholder="cth: 0487121"></div>`;
            }
            panel.innerHTML = html;
            hitungKembalian();
        }

        function tentangTunai(label, id, val) {
            return `<div>
                <label class="text-xs font-bold text-navy-400 uppercase block mb-1">${label}</label>
                <input id="${id}" type="number" min="0" value="${esc(val)}" class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm text-right font-mono">
            </div>`;
        }

        function hitungKembalian() {
            const { total } = hitung();
            const dibayar = dibayarSebenarnya(total);
            byId('modalKembalian').parentElement.classList.toggle('hidden', dibayar < total);
            byId('modalKembalian').textContent = fmt(Math.max(0, dibayar - total));
        }
        function dibayarSebenarnya(total) {
            if (metode === 'cash') return parseFloat(bayarTunai) || 0;
            if (metode === 'split') return Math.max(total, parseFloat(splitTunai) || 0);
            return total; // qris / transfer / debit dianggap lunas penuh.
        }

        // ---------- SUBMIT ----------
        byId('formBayar').addEventListener('submit', e => {
            const { sub, total } = hitung();
            const dibayar = dibayarSebenarnya(total);
            const refEd = byId('refBayar');

            const el = byId('hiddenItems');
            el.innerHTML = '';
            cart.forEach((c, i) => {
                el.insertAdjacentHTML('beforeend',
                    `<input type="hidden" name="items[${i}][gadget_id]" value="${c.id}">
                     <input type="hidden" name="items[${i}][qty]" value="${c.qty}">
                     <input type="hidden" name="items[${i}][harga_jual]" value="${c.jual}">`);
            });
            el.insertAdjacentHTML('beforeend',
                `<input type="hidden" name="diskon" value="${diskon}">
                 <input type="hidden" name="pajak" value="${pajak}">
                 <input type="hidden" name="customer" value="${esc(byId('custName').value.trim())}">
                 <input type="hidden" name="customer_phone" value="${esc(byId('custPhone').value.trim())}">
                 <input type="hidden" name="payment_method" value="${metode}">
                 <input type="hidden" name="paid_amount" value="${dibayar}">
                 <input type="hidden" name="payment_ref" value="${refEd ? esc(refEd.value.trim()) : ''}">`);
            // Nonaktif dulu supaya tidak dobel submit.
            byId('btnProses').disabled = true;
            byId('btnProses').textContent = 'Memproses...';
        });

        // ---------- SHORTCUT ----------
        document.addEventListener('keydown', e => {
            if (e.key === 'F2') { e.preventDefault(); byId('scanInput').focus(); }
            if (e.key === 'F8') { e.preventDefault(); if (!byId('modalBayar').classList.contains('hidden')) return; bukaBayar(); }
            if (e.key === 'F4') { e.preventDefault(); simpanHold(); }
            if (e.key === 'Escape') {
                if (!byId('modalBayar').classList.contains('hidden')) tutupModal();
                else if (byId('scanInput') === document.activeElement) { byId('scanInput').value = ''; }
                else bersihkanCart();
            }
        });

        renderGrid();
        renderCart();
        renderHold();
    </script>
</body>
</html>