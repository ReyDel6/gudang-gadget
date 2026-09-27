<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $penjualan->no_invoice }}</title>
    @php $lebar = request('mode') === '58' ? '220px' : '300px'; @endphp
    <style>
        body { font-family: 'Courier New', monospace; font-size: {{ request('mode') === '58' ? '11px' : '12px' }}; width: {{ $lebar }}; margin: 12px auto; color: #111; }
        .tengah { text-align: center; }
        .hr { border-top: 1px dashed #111; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; }
        .bold { font-weight: bold; }
        .besar { font-size: 16px; }
        .aksi { font-family: Arial, sans-serif; font-size: 13px; margin-bottom: 16px; }
        .aksi a, .aksi button { display: inline-block; margin: 0 4px 4px 0; padding: 8px 14px; border-radius: 8px; text-decoration: none; border: 1px solid #ccc; background: #f6f6f6; color: #111; cursor: pointer; }
        @media print { body { margin: 0; width: auto; } .aksi { display: none; } }
    </style>
</head>
<body>
    <div class="aksi">
        <button onclick="window.print()">Cetak Struk</button>
        @if ($penjualan->customer_phone)
            <a target="_blank" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $penjualan->customer_phone) }}?text={{ urlencode('Halo ' . ($penjualan->customer ?: 'Bapak/Ibu') . ', terima kasih telah berbelanja di Gudang Gadget.' . PHP_EOL . 'No. Nota: ' . $penjualan->no_invoice . PHP_EOL . 'Total: Rp ' . number_format($penjualan->total, 0, ',', '.')) }}">Kirim via WA</a>
        @endif
        <a href="{{ request('mode') === '58' ? request()->url() : request()->url() . '?mode=58' }}">{{ request('mode') === '58' ? 'Lebar 80mm' : 'Lebar 58mm' }}</a>
    </div>

    <div class="tengah besar bold">GUDANG GADGET</div>
    <div class="tengah">Struk Penjualan</div>
    <div class="hr"></div>
    <div class="row"><span>Invoice</span><span><b>{{ $penjualan->no_invoice }}</b></span></div>
    <div class="row"><span>Tanggal</span><span>{{ $penjualan->tanggal->format('d M Y H:i') }}</span></div>
    <div class="row"><span>Customer</span><span>{{ $penjualan->customer ?: 'Umum' }}</span></div>
    @if ($penjualan->customer_phone)
        <div class="row"><span>HP</span><span>{{ $penjualan->customer_phone }}</span></div>
    @endif
    <div class="row"><span>Kasir</span><span>{{ $penjualan->user_name }}</span></div>
    @if ($penjualan->shift?->id)
        <div class="row"><span>Shift</span><span>#{{ $penjualan->shift->id }}</span></div>
    @endif
    <div class="hr"></div>
    @foreach ($penjualan->items as $item)
        <div>{{ $item->nama_produk }}</div>
        <div class="row">
            <span>{{ $item->qty }} x Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
    @endforeach
    <div class="hr"></div>
    <div class="row"><span>Subtotal</span><span>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span></div>
    @if ((float) $penjualan->diskon > 0)
        <div class="row"><span>Diskon</span><span>− Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span></div>
    @endif
    @if ((float) $penjualan->pajak > 0)
        <div class="row"><span>PPN {{ number_format($penjualan->pajak, 0, ',', '.') }}%</span><span>Rp {{ number_format($penjualan->pajak_nominal, 0, ',', '.') }}</span></div>
    @endif
    <div class="row besar bold"><span>TOTAL</span><span>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span></div>
    <div class="hr"></div>
    <div class="row"><span>Bayar</span><span>{{ $penjualan->payment_label }}</span></div>
    <div class="row"><span>Dibayar</span><span>Rp {{ number_format($penjualan->dibayar, 0, ',', '.') }}</span></div>
    <div class="row"><span>Kembalian</span><span>Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</span></div>
    @if ($penjualan->payment_ref)
        <div class="row"><span>Referensi</span><span>{{ $penjualan->payment_ref }}</span></div>
    @endif
    @if ($penjualan->is_void)
        <div class="hr"></div>
        <div class="tengah bold">** DIBATALKAN **</div>
    @endif
    <div class="hr"></div>
    <div class="tengah">Terima kasih! Barang yang sudah dibeli<br>tidak dapat ditukar tanpa nota ini.</div>
    <script>window.print();</script>
</body>
</html>