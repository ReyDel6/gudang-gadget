@php $lebar = request('mode') === '58' ? '220px' : '300px'; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Lunas {{ $servis->no_tiket }} — {{ $store['nama'] }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: {{ request('mode') === '58' ? '11px' : '12px' }}; width: {{ $lebar }}; margin: 0 auto; color: #111; }
        .tengah { text-align: center; }
        .hr { border-top: 1px dashed #000; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .bold { font-weight: 700; }
        .besar { font-size: 1.25em; }
        .aksi { margin: 8px 0; text-align: center; }
        .aksi button, .aksi a { font-family: inherit; font-size: 12px; padding: 6px 10px; margin: 2px; border: 1px solid #333; background: #fff; cursor: pointer; text-decoration: none; color: #111; display: inline-block; }
        .garansi { border: 2px dashed #111; padding: 8px; margin-top: 10px; }
        @media print {
            body { margin: 0; width: auto; }
            .aksi { display: none; }
        }
    </style>
</head>
<body>
    <div class="aksi">
        <button onclick="window.print()">🖨 Cetak Nota &amp; Kartu Garansi</button>
        <a href="{{ request()->url() }}?mode={{ request('mode') === '58' ? '80' : '58' }}">{{ request('mode') === '58' ? '80mm' : '58mm' }}</a>
    </div>

    <div class="tengah">
        <div class="besar">{{ strtoupper($store['nama']) }}</div>
        @if ($store['alamat'])
            <div>{{ $store['alamat'] }}</div>
        @endif
        <div>WA: {{ $store['no_wa'] }}</div>
        <div class="bold">NOTA PELUNASAN SERVIS</div>
    </div>

    <div class="hr"></div>
    <div class="row"><span>No. Tiket</span><span>{{ $servis->no_tiket }}</span></div>
    <div class="row"><span>Tanggal Lunas</span><span>{{ $servis->paid_at?->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Pelanggan</span><span>{{ $servis->customer_name }}</span></div>
    <div class="row"><span>Unit</span><span>{{ $servis->device_brand }} {{ $servis->device_model }}</span></div>

    <div class="hr"></div>
    <div class="bold">RINCIAN BIAYA</div>
    @foreach ($servis->items as $item)
        <div class="row"><span>{{ $item->item_name }} x{{ $item->quantity }}</span><span>Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span></div>
    @endforeach
    <div class="hr"></div>
    <div class="row"><span>Biaya Jasa</span><span>Rp {{ number_format((float) $servis->service_fee, 0, ',', '.') }}</span></div>
    <div class="row"><span>Suku Cadang</span><span>Rp {{ number_format((float) $servis->sparepart_fee, 0, ',', '.') }}</span></div>
    <div class="row"><span class="bold">Total</span><span class="bold">Rp {{ number_format((float) $servis->total_cost, 0, ',', '.') }}</span></div>
    <div class="row"><span>DP Dibayar Awal</span><span>- Rp {{ number_format((float) $servis->down_payment, 0, ',', '.') }}</span></div>
    <div class="row"><span>Dibayar Sekarang</span><span>Rp {{ number_format((float) $servis->paid_amount ?: $servis->remaining_cost, 0, ',', '.') }}</span></div>
    <div class="row"><span class="bold">LUNAS</span><span class="bold">Rp {{ number_format((float) $servis->total_cost, 0, ',', '.') }}</span></div>
    @if ($servis->payment_ref)
        <div class="row"><span>Referensi</span><span>{{ $servis->payment_ref }}</span></div>
    @endif

    <div class="garansi">
        <div class="tengah bold">KARTU GARANSI SERVIS</div>
        <div class="row mt-1"><span>No. Tiket</span><span>{{ $servis->no_tiket }}</span></div>
        <div class="row"><span>Unit</span><span>{{ $servis->device_model }}</span></div>
        <div class="row"><span>Pelanggan</span><span>{{ $servis->customer_name }}</span></div>
        <div class="row"><span>Tanggal Serah Terima</span><span>{{ $servis->delivered_at?->format('d/m/Y') }}</span></div>
        <div class="row"><span>Berlaku Sampai</span><span class="bold">{{ $servis->warranty_until?->format('d/m/Y') }}</span></div>
        <div>Batas klaim garansi servis sesuai jenis perbaikan. Simpan kartu ini sebagai bukti garansi.</div>
    </div>

    <script>window.print();</script>
</body>
</html>