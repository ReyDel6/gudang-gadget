<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota {{ $pembelian->no_invoice }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; width: 320px; margin: 12px auto; color: #111; }
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
    <div class="tengah">Nota Pembelian / Penerimaan Barang</div>
    <div class="hr"></div>
    <div class="row"><span>Invoice</span><span><b>{{ $pembelian->no_invoice }}</b></span></div>
    <div class="row"><span>Tanggal</span><span>{{ $pembelian->tanggal->format('d M Y') }}</span></div>
    <div class="row"><span>Supplier</span><span>{{ $pembelian->supplier ?: '—' }}</span></div>
    <div class="row"><span>Petugas</span><span>{{ $pembelian->user_name }}</span></div>
    <div class="hr"></div>
    @foreach ($pembelian->items as $item)
        <div>{{ $item->nama_produk }}</div>
        <div class="row">
            <span>{{ $item->qty }} x Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</span>
            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
    @endforeach
    <div class="hr"></div>
    <div class="row"><span>Subtotal</span><span>Rp {{ number_format($pembelian->subtotal, 0, ',', '.') }}</span></div>
    @if ((float) $pembelian->diskon > 0)
        <div class="row"><span>Diskon</span><span>− Rp {{ number_format($pembelian->diskon, 0, ',', '.') }}</span></div>
    @endif
    @if ((float) $pembelian->pajak > 0)
        <div class="row"><span>PPN {{ number_format($pembelian->pajak, 0, ',', '.') }}%</span><span>Rp {{ number_format($pembelian->pajak_nominal, 0, ',', '.') }}</span></div>
    @endif
    <div class="row besar bold"><span>TOTAL</span><span>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span></div>
    <script>window.print();</script>
</body>
</html>