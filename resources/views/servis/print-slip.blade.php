@php $lebar = request('mode') === '58' ? '220px' : '300px'; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>STT {{ $servis->no_tiket }} — {{ $store['nama'] }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: {{ request('mode') === '58' ? '11px' : '12px' }}; width: {{ $lebar }}; margin: 0 auto; color: #111; }
        .tengah { text-align: center; }
        .hr { border-top: 1px dashed #000; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .bold { font-weight: 700; }
        .besar { font-size: 1.25em; }
        .aksi { margin: 8px 0; text-align: center; }
        .aksi button, .aksi a { font-family: inherit; font-size: 12px; padding: 6px 10px; margin: 2px; border: 1px solid #333; background: #fff; cursor: pointer; text-decoration: none; color: #111; display: inline-block; }
        .svc { background: #111; color: #fff; padding: 2px 8px; font-weight: 700; }
        @media print {
            body { margin: 0; width: auto; }
            .aksi { display: none; }
        }
    </style>
</head>
<body>
    <div class="aksi">
        <button onclick="window.print()">🖨 Cetak</button>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $store['no_wa']) }}?text={{ rawurlencode('Konfirmasi tanda terima servis No. ' . $servis->no_tiket) }}" target="_blank">Kirim via WA</a>
        <a href="{{ request()->url() }}?mode={{ request('mode') === '58' ? '80' : '58' }}">{{ request('mode') === '58' ? '80mm' : '58mm' }}</a>
    </div>

    <div class="tengah">
        <div class="besar">{{ strtoupper($store['nama']) }}</div>
        @if ($store['alamat'])
            <div>{{ $store['alamat'] }}</div>
        @endif
        <div>WA: {{ $store['no_wa'] }}</div>
        <div class="svc">SURAT TANDA TERIMA SERVIS</div>
    </div>

    <div class="hr"></div>
    <div class="row"><span>No. Tiket</span><span class="bold">{{ $servis->no_tiket }}</span></div>
    <div class="row"><span>Tanggal Masuk</span><span>{{ $servis->received_at?->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Frontdesk</span><span>{{ $servis->cashier?->name }}</span></div>
    <div class="row"><span>Kasir / Pelanggan</span><span>{{ $servis->customer_name }}</span></div>
    <div class="row"><span>No. HP / WA</span><span>{{ $servis->customer_phone }}</span></div>
    @if ($servis->customer_address)
        <div class="row"><span>Alamat</span><span>{{ $servis->customer_address }}</span></div>
    @endif
    @if ($servis->technician)
        <div class="row"><span>Teknisi</span><span>{{ $servis->technician->name }}</span></div>
    @endif

    <div class="hr"></div>
    <div class="bold">UNIT / GADGET</div>
    <div class="row"><span>Merk</span><span>{{ $servis->device_brand }}</span></div>
    <div class="row"><span>Model</span><span>{{ $servis->device_model }}</span></div>
    @if ($servis->imei_or_serial)
        <div class="row"><span>IMEI/SN</span><span>{{ $servis->imei_or_serial }}</span></div>
    @endif
    @if ($servis->passcode)
        <div class="row"><span>Pola/PIN</span><span>{{ $servis->passcode }}</span></div>
    @endif
    @if ($servis->completeness)
        <div class="row"><span>Kelengkapan</span><span>{{ $servis->completeness }}</span></div>
    @endif
    @if ($servis->initial_condition)
        <div class="bold mt-1">KONDISI AWAL / MINUS</div>
        <div>{{ $servis->initial_condition }}</div>
    @endif

    <div class="hr"></div>
    <div class="bold">KELUHAN PELANGGAN</div>
    <div>{{ $servis->problem_description }}</div>

    <div class="hr"></div>
    <div class="row"><span>Uang Muka (DP)</span><span>Rp {{ number_format((float) $servis->down_payment, 0, ',', '.') }}</span></div>
    <div class="row"><span>Estimasi Jasa</span><span>Rp {{ number_format((float) $servis->service_fee, 0, ',', '.') }}</span></div>
    <div class="row"><span>Estimasi Part</span><span>Rp {{ number_format((float) $servis->sparepart_fee, 0, ',', '.') }}</span></div>
    <div class="row"><span class="bold">Total</span><span class="bold">Rp {{ number_format((float) $servis->total_cost, 0, ',', '.') }}</span></div>
    <div class="row"><span>Garansi</span><span>{{ $servis->warranty_days }} hari</span></div>

    <div class="hr"></div>
    <div class="tengah">{!! $barcode !!}</div>
    <div class="tengah bold">{{ $servis->no_tiket }}</div>
    <div class="tengah">Pantau status: gudanggadget.online/shop/tracking-service</div>

    <div class="hr"></div>
    <div class="bold tengah">SYARAT &amp; KETENTUAN</div>
    <div>1. Pihak toko tidak bertanggung jawab atas data HP sebelum diolah bagi unit diserahkan tanpa izin reset.</div>
    <div>2. Garansi servis berlaku sejak unit diserahkan kembali ke pelanggan & sesuai jenis perbaikan (batas klaim tercantum pada nota lunas).</div>
    <div>3. Garansi batal bila justru kerusakan baru terjadi akibat jatuh, basah, atau dibongkar pihak lain.</div>
    <div>4. Barang yang tidak diambil melebihi 90 hari bukan menjadi tanggung jawab toko.</div>

    <script>window.print();</script>
</body>
</html>