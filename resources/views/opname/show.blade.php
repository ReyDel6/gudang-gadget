@extends('layouts.app')

@section('title', 'Opname ' . $opname->no_invoice)

@section('content')

    <div class="flex items-center justify-between mb-6 print-hidden">
        <div>
            <a href="{{ route('opname.index') }}" class="text-xs font-semibold text-navy-400 hover:text-gold-600">← Kembali ke daftar</a>
            <h1 class="text-xl font-bold text-navy-800 mt-1">Opname {{ $opname->no_invoice }}</h1>
        </div>
        <div class="flex gap-2">
            @if ($opname->status === \App\Models\StokOpname::ST_IN_PROGRESS)
                <form method="POST" action="{{ route('opname.batal', $opname->id) }}"
                      onsubmit="return confirm('Batalkan opname ini? Hasil scan dibuang.')">
                    @csrf
                    <button class="border border-rose-200 text-rose-600 hover:bg-rose-50 text-sm font-semibold px-4 py-2 rounded-lg">Batalkan</button>
                </form>
                <form method="POST" action="{{ route('opname.selesai', $opname->id) }}"
                      onsubmit="return confirm('Selesai? Semua selisih akan disesuaikan ke gudang dan tercatat sebagai mutasi.')">
                    @csrf
                    <button class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg">Selesai & Sesuaikan</button>
                </form>
            @else
                <a href="{{ route('opname.cetak', $opname->id) }}"
                   class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg">Cetak Berita Acara</a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm font-semibold mb-5 print-hidden">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Stok Sistem</p>
            <p class="text-2xl font-black text-navy-800 mt-0.5">{{ number_format($opname->total_system_items) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Stok Fisik Terhitung</p>
            <p class="text-2xl font-black text-navy-800 mt-0.5">{{ number_format($opname->total_physical_items) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Selisih</p>
            <p class="text-2xl font-black {{ ($opname->total_difference ?? 0) == 0 ? 'text-navy-800' : 'text-gold-700' }} mt-0.5">
                {{ number_format($opname->total_difference) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-4">
            <p class="text-xs font-bold text-navy-400 uppercase tracking-wide">Kategori</p>
            <p class="text-lg font-bold text-navy-800 mt-0.5 truncate">{{ $opname->category_filter ?: 'Semua' }}</p>
            <p class="text-xs text-navy-400 mt-1">oleh {{ $opname->auditor?->name }}</p>
        </div>
    </div>

    @if ($opname->status === \App\Models\StokOpname::ST_IN_PROGRESS)
        <div class="bg-navy-900 text-white rounded-2xl p-5 mb-6 print-hidden">
            <p class="text-xs font-bold text-gold-400 uppercase tracking-widest mb-2">Scan Unit Fisik</p>
            <div class="flex gap-3">
                <input id="scanSku" type="text" placeholder="Scan / ketik SKU lalu Enter..."
                       autofocus autocomplete="off"
                       class="flex-1 rounded-xl border-2 border-gold-500 bg-navy-800 text-white px-4 py-3 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gold-500">
                <button class="bg-gold-500 text-navy-900 font-black px-6 rounded-xl" onclick="prosesScan()">Scan</button>
            </div>
            <div id="scanPesan" class="mt-3 text-sm font-semibold hidden"></div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-navy-100 flex items-center justify-between">
            <h2 class="font-black text-navy-800">
                Daftar Item
                <span class="ml-2 text-xs font-bold rounded-full px-2 py-0.5 bg-gold-100 text-gold-700">{{ $terhitung }}/{{ $opname->items()->count() }} terhitung</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">SKU</th>
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Sistem</th>
                        <th class="px-4 py-3 font-medium">Fisik</th>
                        <th class="px-4 py-3 font-medium">Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($items as $row)
                        <tr class="{{ is_null($row->physical_stock) ? '' : 'bg-emerald-50/40' }}">
                            <td class="px-4 py-3 font-mono text-navy-700">{{ $row->gadget?->sku ?: '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-navy-800">{{ $row->gadget?->nama_produk }}</td>
                            <td class="px-4 py-3 font-mono text-navy-700">{{ $row->system_stock }}</td>
                            <td class="px-4 py-3 font-mono text-navy-700">{{ $row->physical_stock ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono font-bold {{ ! is_null($row->difference) && $row->difference != 0 ? 'text-gold-700' : 'text-navy-400' }}">
                                {{ is_null($row->difference) ? '—' : number_format($row->difference) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-navy-400">Belum ada item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-navy-100">
            {{ $items->links() }}
        </div>
    </div>

    @php
        // Data SKU untuk quick-scan tanpa round-trip (fallback pencarian lokal).
        $skus = $opname->items()->with('gadget')->get()
            ->filter(fn ($r) => $r->gadget?->sku)
            ->mapWithKeys(fn ($r) => [$r->gadget->sku => ['id' => $r->id, 'nama' => $r->gadget->nama_produk]])
            ->toArray();
    @endphp

    <script>
        const skus = @json($skus);
        const selScan = document.getElementById('scanSku');
        const pesan = document.getElementById('scanPesan');

        function tampilPesan(teks, oke) {
            pesan.textContent = teks;
            pesan.classList.remove('hidden', 'text-emerald-300', 'text-rose-300');
            pesan.classList.add(oke ? 'text-emerald-300' : 'text-rose-300');
        }

        async function prosesScan() {
            const v = selScan.value.trim();
            selScan.value = '';
            if (!v) return;
            pesan.classList.add('hidden');
            try {
                const res = await fetch('{{ route('opname.scan', $opname->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ sku: v })
                });
                const data = await res.json();
                if (!res.ok) {
                    tampilPesan('✖ ' + (data.message || 'Gagal scan.'), false);
                    return;
                }
                tampilPesan('✓ ' + data.nama + ' — fisik ' + data.physical + ' (sistem ' + data.system + ', selisih ' + data.difference + ')', true);
            } catch (e) {
                tampilPesan('✖ Gagal terhubung.', false);
            }
        }

        if (selScan) {
            selScan.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); prosesScan(); } });
            selScan.focus();
        }
    </script>

@endsection