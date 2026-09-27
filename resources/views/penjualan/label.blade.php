<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Pengiriman {{ $penjualan->no_invoice }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #111; margin: 24px; }
        .aksi { margin-bottom: 16px; }
        .aksi a, .aksi button { display: inline-block; margin: 0 4px 4px 0; padding: 8px 14px; border-radius: 8px;
            text-decoration: none; border: 1px solid #ccc; background: #f6f6f6; color: #111; cursor: pointer; font-size: 13px; }
        .label { width: 100%; max-width: 560px; border: 2px dashed #222; border-radius: 12px; padding: 22px; background: #fff; }
        .header { display: flex; justify-content: space-between; align-items: baseline; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 14px; }
        .header h1 { font-size: 18px; margin: 0; }
        .ref { font-size: 12px; color: #555; }
        .blok { margin-bottom: 16px; }
        .blok .judul { font-size: 10px; font-weight: 800; letter-spacing: 1.5px; color: #666; text-transform: uppercase; margin-bottom: 4px; }
        .blok .isi { font-size: 15px; font-weight: 700; line-height: 1.4; }
        .blok .kecil { font-size: 13px; font-weight: 500; color: #333; }
        .keterangan { font-size: 11px; color: #555; }
        @media print { .aksi { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="aksi">
        <button onclick="window.print()">Cetak Label</button>
        <a href="javascript:history.back()">Kembali</a>
    </div>

    <div class="label">
        <div class="header">
            <h1>LABEL PENGIRIMAN</h1>
            <span class="ref">REF: {{ $penjualan->no_invoice }}</span>
        </div>

        <div class="blok">
            <div class="judul">Pengirim</div>
            <div class="isi">{{ $penjualan->sender_name ?: '—' }}</div>
            @if ($penjualan->sender_phone)
                <div class="kecil">📞 {{ $penjualan->sender_phone }}</div>
            @endif
        </div>

        <div class="blok">
            <div class="judul">Penerima</div>
            <div class="isi">{{ $penjualan->recipient_name ?: '—' }}</div>
            <div class="kecil" style="white-space:pre-line">{{ $penjualan->recipient_address }}</div>
        </div>

        <div class="blok">
            <div class="judul">Isi Paket</div>
            <div class="kecil">
                @foreach ($penjualan->items as $item)
                    {{ $item->qty }}× {{ $item->nama_produk }}<br>
                @endforeach
            </div>
        </div>

        <div class="keterangan">
            Kirim via ekspedisi pilihan Anda. Tanggal: {{ $penjualan->tanggal->format('d M Y') }}.
        </div>
    </div>

    <script>window.print();</script>
</body>
</html>