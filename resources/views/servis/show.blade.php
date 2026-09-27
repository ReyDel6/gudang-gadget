@extends('layouts.app')

@section('title', 'Detail Servis ' . $servis->no_tiket)

@php
    $bisaTeknisi = \Illuminate\Support\Facades\Auth::user()?->isTeknisi();
    $bisaKasir = ! $bisaTeknisi;
    $statusBuka = array_diff_key(\App\Models\ServiceTicket::STATUS, [\App\Models\ServiceTicket::STATUS_DIAMBIL => true]);
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl font-bold text-navy-800">{{ $servis->no_tiket }}</h1>
                <span class="inline-flex text-[11px] font-black px-2 py-1 rounded-full {{ $servis->status_warna }}">{{ $servis->status_label }}</span>
                @if ($servis->is_garansi)
                    <span class="inline-flex text-[10px] font-black px-2 py-1 rounded-full bg-gold-100 text-gold-700">KLAIM GARANSI</span>
                @endif
            </div>
            <p class="text-sm text-navy-400">{{ $servis->device_brand }} {{ $servis->device_model }} · Diterima {{ $servis->received_at?->format('d M Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap print-hidden">
            @if ($bisaKasir)
                <a href="{{ route('servis.cetak', $servis->id) }}" target="_blank"
                   class="bg-white border border-navy-200 hover:bg-navy-50 text-navy-800 text-sm font-semibold px-4 py-2.5 rounded-xl">Cetak Slip</a>
                @if ($servis->status === \App\Models\ServiceTicket::STATUS_DIAMBIL)
                    <a href="{{ route('servis.cetak-lunas', $servis->id) }}" target="_blank"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl">Nota Lunas &amp; Kartu Garansi</a>
                @else
                    <a href="{{ route('servis.lunas', $servis->id) }}"
                       class="bg-gold-500 hover:bg-gold-600 text-navy-900 text-sm font-black px-4 py-2.5 rounded-xl">Bayar / Lunas</a>
                @endif
            @endif
            <a href="{{ route('servis.index') }}" class="text-sm font-semibold text-navy-500 hover:text-navy-800">← Daftar</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <h2 class="text-xs font-black uppercase tracking-wide text-navy-400 mb-3">Pelanggan</h2>
            <p class="font-bold text-navy-900">{{ $servis->customer_name }}</p>
            <p class="text-sm text-navy-500">{{ $servis->customer_phone }}</p>
            @if ($servis->customer_address)
                <p class="text-sm text-navy-500 mt-1">{{ $servis->customer_address }}</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <h2 class="text-xs font-black uppercase tracking-wide text-navy-400 mb-3">Unit &amp; Keluhan</h2>
            <p class="font-bold text-navy-900">{{ $servis->device_model }}</p>
            <p class="text-sm text-navy-500">{{ $servis->device_brand }}{{ $servis->imei_or_serial ? ' · IMEI/SN: ' . $servis->imei_or_serial : '' }}</p>
            @if ($servis->passcode)
                <p class="text-sm text-navy-500 mt-1">Pola/PIN: <span class="font-mono">{{ $servis->passcode }}</span></p>
            @endif
            <p class="text-xs text-navy-500 mt-2">{{ $servis->problem_description }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <h2 class="text-xs font-black uppercase tracking-wide text-navy-400 mb-3">Cashier &amp; Teknisi</h2>
            <p class="text-sm text-navy-700">Diterima: <span class="font-semibold">{{ $servis->cashier?->name }}</span></p>
            <p class="text-sm text-navy-700 mt-1">Teknisi: <span class="font-semibold">{{ $servis->technician?->name ?? '—' }}</span></p>
            @if ($servis->parent)
                <p class="text-xs text-gold-700 mt-2">Garansi dari <a href="{{ route('servis.show', $servis->parent_id) }}" class="underline font-bold">{{ $servis->parent->no_tiket }}</a></p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-black uppercase tracking-wide text-navy-800">Pekerjaan &amp; Suku Cadang</h2>
                    <span class="text-xs text-navy-400">{{ $servis->items_count ?? $servis->items->count() }} item</span>
                </div>

                <div class="overflow-x-auto mb-5">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-navy-400 border-b border-navy-100">
                                <th class="px-3 py-2 font-bold">Tipe</th>
                                <th class="px-3 py-2 font-bold">Item</th>
                                <th class="px-3 py-2 font-bold text-center">Qty</th>
                                <th class="px-3 py-2 font-bold text-right">Harga</th>
                                <th class="px-3 py-2 font-bold text-right">Subtotal</th>
                                @if (! $servis->isSelesai())
                                    <th class="px-3 py-2"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-50">
                            @forelse ($servis->items as $item)
                                <tr>
                                    <td class="px-3 py-2.5">
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $item->item_type === \App\Models\ServiceTicketItem::TIPE_SPAREPART ? 'bg-blue-50 text-blue-700' : 'bg-navy-50 text-navy-700' }}">
                                            {{ $item->tipe_label }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 font-semibold text-navy-800">
                                        {{ $item->item_name }}
                                        @if ($item->gadget)
                                            <p class="text-[11px] text-navy-400">{{ $item->gadget->sku }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-navy-600">{{ $item->quantity }}</td>
                                    <td class="px-3 py-2.5 text-right text-navy-600">Rp {{ number_format((float) $item->sell_price, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2.5 text-right font-bold text-navy-900">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                    @if (! $servis->isSelesai())
                                        <td class="px-3 py-2.5 text-right">
                                            <form method="POST" action="{{ route('servis.item.hapus', [$servis->id, $item->id]) }}"
                                                  onsubmit="return confirm('Hapus item ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-700 text-sm font-bold">Hapus</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-8 text-center text-navy-400">Belum ada item. Tambahkan jasa atau suku cadang di bawah ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (! $servis->isSelesai())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('servis.item.tambah', $servis->id) }}" class="rounded-xl border border-navy-100 p-4">
                            @csrf
                            <input type="hidden" name="item_type" value="service_fee">
                            <h3 class="text-xs font-black uppercase tracking-wide text-navy-600 mb-3">Tambah Jasa</h3>
                            <div class="space-y-2.5">
                                <input type="text" name="item_name" placeholder="Nama jasa (mis. Jasa Ganti LCD)"
                                       class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" step="0.01" min="0" name="sell_price" placeholder="Biaya jasa (Rp)"
                                           class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                                    <input type="number" min="1" max="99" name="quantity" value="1"
                                           class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                                </div>
                                <button type="submit" class="w-full bg-navy-800 hover:bg-navy-700 text-white text-xs font-bold py-2 rounded-lg">Tambah Jasa</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('servis.item.tambah', $servis->id) }}" class="rounded-xl border border-navy-100 p-4">
                            @csrf
                            <input type="hidden" name="item_type" value="sparepart">
                            <h3 class="text-xs font-black uppercase tracking-wide text-navy-600 mb-3">Tambah Suku Cadang (Gudang)</h3>
                            <div class="space-y-2.5">
                                <select name="gadget_id" required class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                                    <option value="">— Pilih spare part —</option>
                                    @foreach ($spareparts as $sp)
                                        <option value="{{ $sp->id }}">{{ $sp->nama_produk }} — Stok {{ $sp->stock }}</option>
                                    @endforeach
                                </select>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" step="0.01" min="0" name="sell_price" placeholder="Harga jual (Rp)"
                                           class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                                    <input type="number" min="1" max="99" name="quantity" value="1"
                                           class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                                </div>
                                <p class="text-[11px] text-navy-400">Stok gudang otomatis terpotong (catatan mutasi <b>Pengeluaran — Pemakaian Servis</b>).</p>
                                <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold py-2 rounded-lg">Pakai Spare part</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Riwayat Status / Timeline</h2>
                <div class="space-y-3">
                    @forelse ($servis->logs as $log)
                        <div class="flex items-start gap-3">
                            <div class="mt-1.5 w-2.5 h-2.5 rounded-full bg-gold-500 shrink-0"></div>
                            <div>
                                <p class="text-sm font-semibold text-navy-800">
                                    {{ $log->status_label }}
                                    <span class="text-xs font-normal text-navy-400">· {{ $log->created_at->format('d/m/Y H:i') }} · {{ $log->user?->name }}</span>
                                </p>
                                @if ($log->notes)
                                    <p class="text-xs text-navy-500 mt-0.5">{{ $log->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-navy-400">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Biaya &amp; Tagihan</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-navy-500">Biaya Jasa</dt><dd class="font-semibold">Rp {{ number_format((float) $servis->service_fee, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-navy-500">Suku Cadang</dt><dd class="font-semibold">Rp {{ number_format((float) $servis->sparepart_fee, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between border-t border-navy-100 pt-2"><dt class="font-black text-navy-800">Total</dt><dd class="font-black text-navy-900">Rp {{ number_format((float) $servis->total_cost, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-navy-500">Uang Muka (DP)</dt><dd class="font-semibold text-navy-700">- Rp {{ number_format((float) $servis->down_payment, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="font-black text-navy-800">Sisa Tagihan</dt><dd class="font-black {{ $servis->remaining_cost > 0 ? 'text-gold-600' : 'text-emerald-600' }}">Rp {{ number_format((float) $servis->remaining_cost, 0, ',', '.') }}</dd></div>
                    @if ($servis->payment_method)
                        <div class="flex justify-between"><dt class="text-navy-500">Metode Lunas</dt><dd class="font-semibold">{{ \App\Support\Pembayaran::labelMetode($servis->payment_method) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-navy-500">Garansi s/d</dt><dd class="font-semibold text-emerald-700">{{ $servis->warranty_until?->format('d M Y') }}</dd></div>
                    @endif
                </dl>
                @if (! $servis->isSelesai() && $bisaKasir)
                    <a href="{{ route('servis.lunas', $servis->id) }}"
                       class="mt-4 block text-center bg-gold-500 hover:bg-gold-600 text-navy-900 text-sm font-black py-2.5 rounded-xl">Pelunasan Kasir / POS</a>
                @endif
            </div>

            @if (! $servis->isSelesai())
                <form method="POST" action="{{ route('servis.status', $servis->id) }}" class="bg-white rounded-2xl border border-navy-100 p-5">
                    @csrf
                    <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Update Status</h2>
                    <div class="space-y-2.5">
                        <select name="status" required class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            @foreach ($statusBuka as $kode => $label)
                                <option value="{{ $kode }}" @selected(old('status', $servis->status) === $kode)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="technician_id" class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <option value="">Teknisi: — (tidak diubah) —</option>
                            @foreach ($teknisi as $t)
                                <option value="{{ $t->id }}" @selected($servis->technician_id === $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                        <textarea name="notes" rows="2" placeholder="Catatan pekerjaan (opsional)"
                                  class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500"></textarea>
                        <button type="submit" class="w-full bg-navy-800 hover:bg-navy-700 text-white text-xs font-bold py-2.5 rounded-lg">Simpan Status</button>
                    </div>
                </form>
            @endif

            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Quality Check</h2>
                @if ($servis->isSelesai())
                    <p class="text-xs text-navy-400 mb-3">Checklist terakhir tersimpan ({{ count($servis->qc_checklist ?? []) }}/{{ count(\App\Models\ServiceTicket::QC_ITEMS) }} lolos).</p>
                @endif
                <form method="POST" action="{{ route('servis.qc', $servis->id) }}">
                    @csrf
                    <div class="space-y-2">
                        @foreach (\App\Models\ServiceTicket::QC_ITEMS as $key => $label)
                            <label class="flex items-center gap-2 text-sm text-navy-700">
                                <input type="checkbox" name="qc_checklist[]" value="{{ $key }}"
                                       @checked(in_array($key, array_values($servis->qc_checklist ?? [])))>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <input type="text" name="notes" placeholder="Catatan uji fungsi (opsional)"
                           class="mt-3 w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <button type="submit" class="mt-3 w-full border border-navy-200 hover:bg-navy-50 text-navy-800 text-xs font-bold py-2.5 rounded-lg">Simpan QC</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Kirim Notifikasi WhatsApp</h2>
                <div class="space-y-2">
                    <a href="{{ $waLinks['tanda_terima'] }}" target="_blank" class="block text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-lg">Kirim Tanda Terima Masuk</a>
                    <a href="{{ $waLinks['konfirmasi_biaya'] }}" target="_blank" class="block text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-lg">Konfirmasi Biaya / Persetujuan</a>
                    <a href="{{ $waLinks['siap_diambil'] }}" target="_blank" class="block text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-lg">Notif Unit Selesai &amp; Siap Diambil</a>
                </div>
            </div>

            @if ($servis->status === \App\Models\ServiceTicket::STATUS_DIAMBIL && $bisaKasir)
                <div class="bg-white rounded-2xl border border-rose-200 p-5">
                    <h2 class="text-sm font-black uppercase tracking-wide text-rose-700 mb-4">Klaim Garansi / Re-work</h2>
                    <p class="text-xs text-navy-500 mb-3">Pelanggan kembali dengan keluhan dalam masa garansi (hingga {{ $servis->warranty_until?->format('d M Y') }}). Buat tiket baru biaya jasa Rp 0.</p>
                    <form method="POST" action="{{ route('servis.garansi', $servis->id) }}">
                        @csrf
                        <textarea name="problem_description" rows="2" required placeholder="Keluhan garansi pelanggan..."
                                  class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400"></textarea>
                        <button type="submit" class="mt-3 w-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2.5 rounded-lg">Buat Tiket Garansi</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

@endsection