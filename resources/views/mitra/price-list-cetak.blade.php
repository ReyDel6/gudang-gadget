<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Price List {{ $settings['store_name'] }} — {{ $tanggal->format('d M Y') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a2033; font-size: 12px; margin: 24px; }
        .aksi { margin-bottom: 16px; }
        .aksi a, .aksi button { display: inline-block; margin: 0 4px 4px 0; padding: 8px 14px; border-radius: 8px;
            text-decoration: none; border: 1px solid #ccc; background: #f6f6f6; color: #111; cursor: pointer; font-size: 13px; }
        .judul { text-align: center; margin-bottom: 18px; }
        .judul h1 { margin: 0 0 4px; font-size: 20px; }
        .judul p { margin: 0; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a2033; color: #fff; text-align: left; padding: 8px 10px; }
        td { padding: 7px 10px; border-bottom: 1px solid #e3e6ee; vertical-align: top; }
        tr:nth-child(even) td { background: #f7f8fb; }
        .angka { text-align: right; white-space: nowrap; }
        .mitra { font-weight: 800; }
        .keterangan { font-size: 11px; color: #555; }
        .footer { margin-top: 14px; font-size: 11px; color: #777; }
        @media print { .aksi { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="aksi">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <div class="judul">
        <h1>Price List Mitra — {{ $settings['store_name'] }}</h1>
        <p>Berlaku {{ $tanggal->format('d M Y') }} · {{ $products->count() }} produk ready stock</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Produk</th>
                <th class="angka">Harga Retail</th>
                <th class="angka">Harga Mitra</th>
                <th>Stok</th>
                <th>Keterangan Tier</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->nama_produk }}</td>
                    <td class="angka">{{ 'Rp ' . number_format($p->harga_aktif, 0, ',', '.') }}</td>
                    <td class="angka mitra">Rp {{ number_format($p->harga_mitra, 0, ',', '.') }}</td>
                    <td>{{ (int) $p->stock }}</td>
                    <td class="keterangan">
                        @if ($p->tierPrices->isNotEmpty())
                            @foreach ($p->tierPrices->sortBy('min_qty') as $t)
                                {{ $t->tier_name }} min {{ $t->min_qty }}: Rp {{ number_format((float) $t->price, 0, ',', '.') }}{{ !$loop->last ? ' · ' : '' }}
                            @endforeach
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">* Harga mitra adalah tarif partai/terendah. Harga dapat berubah sewaktu-waktu; konfirmasi ketersediaan stok sebelum order.</p>

    <script>window.print();</script>
</body>
</html>