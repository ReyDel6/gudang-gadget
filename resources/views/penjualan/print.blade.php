<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $penjualan->no_invoice }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 300px; margin: 12px auto; color: #111; }
        .tengah { text-align: center; }
        .hr { border-top: 1px dashed #111; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; }
        .bold { font-weight: bold; }
        .besar { font-size: 16px; }
        @media print { body { margin: 0; width: auto; } }
    </style>
</head>
<body>
    <div class="tengah besar bold">GUDANG GADGET</div>
    <div class="tengah">Struk Penjualan</div>
    <div class="hr"></div>
    <div class="row"><span>Invoice</span><span><b>{{ $penjualan->no_invoice }}</b></span></div>
    <div class="row"><span>Tanggal</span><span>{{ $penjualan->tanggal->format('d M Y') }}</span></div>
    <div class="row"><span>Customer</span><span>{{ $penjualan->customer ?: 'Umum' }}</span></div>
    <div class="row"><span>Kasir</span><span>{{ $penjualan->user_name }}</span></div>
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
    <div class="tengah">Terima kasih! Barang yang sudah dibeli<br>tidak dapat ditukar tanpa nota ini.</div>
    <script>window.print();</script>
</body>
</html>