<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Gadget;
use App\Models\GadgetImei;
use App\Models\Penjualan;
use App\Models\StokLog;
use App\Models\User;
use App\Support\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PenjualanService
{
    public static function aturan(): array
    {
        return [
            'tanggal' => ['nullable', 'date'],
            'customer' => ['nullable', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_type' => ['nullable', 'string', Rule::in(['retail', 'reseller'])],
            'is_dropship' => ['nullable', 'boolean'],
            'sender_name' => ['nullable', 'string', 'max:150'],
            'sender_phone' => ['nullable', 'string', 'max:30'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'recipient_address' => ['nullable', 'string', 'max:500'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.gadget_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_jual' => ['nullable', 'numeric', 'min:0'],
            'items.*.imei' => ['nullable', 'array', 'max:500'],
            'items.*.imei.*' => ['nullable', 'string', 'max:50'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'pajak' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_method' => ['nullable', 'string', Rule::in(array_keys(Pembayaran::METODE))],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_ref' => ['nullable', 'string', 'max:100'],
            'trade_in_value' => ['nullable', 'numeric', 'min:0'],
            'trade_in_desc' => ['nullable', 'string', 'max:200'],
            'trade_in_imei' => ['nullable', 'string', 'max:50'],
        ];
    }

    public static function pesan(): array
    {
        return [
            'items.required' => 'Minimal satu item penjualan.',
            'items.*.qty.min' => 'Jumlah item harus minimal 1.',
            'items.*.gadget_id.exists' => 'Produk yang dipilih tidak valid.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'customer_type.in' => 'Tipe pelanggan tidak valid.',
            'is_dropship.boolean' => 'Nilai pengiriman dropship tidak valid.',
        ];
    }

    public static function aturanTambahan(array $data): array
    {
        $tipe = $data['customer_type'] ?? 'retail';
        $dropship = ! empty($data['is_dropship']) && filter_var($data['is_dropship'], FILTER_VALIDATE_BOOL);

        return [
            'customer' => array_filter([
                $tipe === 'reseller' ? 'required' : 'nullable',
                'string',
                'max:150',
            ]),
            'recipient_name' => array_filter([
                $dropship ? 'required' : 'nullable',
                'string',
                'max:150',
            ]),
            'recipient_address' => array_filter([
                $dropship ? 'required' : 'nullable',
                'string',
                'max:500',
            ]),
        ];
    }

    /**
     * Proses transaksi penjualan secara atomik dengan row-lock,
     * setoran otomatis dari stok, log retur/history, dan pencatatan audit.
     */
    public static function buat(array $data, ?User $user, ?string $ip, ?int $shiftId = null): Penjualan
    {
        $diskon = (float) ($data['diskon'] ?? 0);
        $pajakPersen = (float) ($data['pajak'] ?? 0);
        $metode = $data['payment_method'] ?? Pembayaran::CASH;
        $tradeInValue = round((float) ($data['trade_in_value'] ?? 0), 2);
        $tanggal = $data['tanggal'] ?? now()->toDateString();
        $tipe = $data['customer_type'] ?? 'retail';
        $isDropship = ! empty($data['is_dropship']) && filter_var($data['is_dropship'], FILTER_VALIDATE_BOOL);

        return DB::transaction(function () use ($data, $user, $ip, $shiftId, $diskon, $pajakPersen, $metode, $tradeInValue, $tanggal, $tipe, $isDropship) {
            $no = InvoiceService::buat('PJ', 'penjualans');

            $penjualan = Penjualan::create([
                'no_invoice' => $no,
                'tanggal' => $tanggal,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'customer' => $data['customer'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_type' => $tipe,
                'is_dropship' => $isDropship,
                'sender_name' => $data['sender_name'] ?? null,
                'sender_phone' => $data['sender_phone'] ?? null,
                'recipient_name' => $data['recipient_name'] ?? null,
                'recipient_address' => $data['recipient_address'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'total' => 0,
                'diskon' => $diskon,
                'pajak' => $pajakPersen,
                'payment_method' => $metode,
                'payment_ref' => $data['payment_ref'] ?? null,
                'payment_status' => 'paid',
                'cashier_shift_id' => $shiftId,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $gadget = StokService::adjust(
                    Gadget::findOrFail($item['gadget_id']),
                    StokLog::TIPE_PENGELUARAN,
                    -((int) $item['qty']),
                    "Penjualan {$no}"
                );

                $qty = (int) $item['qty'];
                $retail = (float) $gadget->harga_jual ?: (float) $gadget->harga_beli;
                $hargaJual = (float) ($item['harga_jual'] ?? $retail);

                // FR-3.2: tipe transaksi Mitra Reseller memakai tier
                // grosir/partai sesuai jumlah (diskon hanya saat min jumlah terpenuhi).
                if ($tipe === 'reseller') {
                    $hargaJual = $gadget->hargaUntuk($qty);
                } else {
                    // Auto-tier harga grosir/partai: hanya saat kasir mengirimkan
                    // harga standar (retail/tier), bukan harga manual/kustom.
                    $tiers = $gadget->tierPrices()->orderByDesc('min_qty')->get();
                    if ($tiers->isNotEmpty() && $hargaJual > 0) {
                        $dikenal = collect([$retail])->merge($tiers->map(fn ($t) => (float) $t->price))
                            ->map(fn ($v) => round($v, 2))->unique()->all();
                        if (in_array(round($hargaJual, 2), $dikenal, true)) {
                            foreach ($tiers as $tier) {
                                if ($qty >= (int) $tier->min_qty && (float) $tier->price > 0) {
                                    $hargaJual = round((float) $tier->price, 2);
                                    break;
                                }
                            }
                        }
                    }
                }

                $subtotal = round($hargaJual * $qty, 2);
                $total += $subtotal;

                $itemBaris = $penjualan->items()->create([
                    'gadget_id' => $gadget->id,
                    'nama_produk' => $gadget->nama_produk,
                    'harga_beli' => $gadget->harga_beli,
                    'harga_jual' => $hargaJual,
                    'qty' => (int) $item['qty'],
                    'subtotal' => $subtotal,
                ]);

                $imeiTerpilih = collect($item['imei'] ?? [])
                    ->map(fn ($v) => trim((string) $v))
                    ->filter(fn ($v) => $v !== '')
                    ->values()->all();

                if ($gadget->imeis()->exists() || $imeiTerpilih) {
                    // Produk ber-IMEI wajib memilih tepat satu IMEI per unit fisik.
                    if (count($imeiTerpilih) !== $qty) {
                        throw ValidationException::withMessages([
                            "items.*.imei" => "Produk \"{$gadget->nama_produk}\" wajib pilih {$qty} nomor IMEI unit yang diserahkan.",
                        ]);
                    }

                    $imeis = GadgetImei::whereIn('imei', $imeiTerpilih)
                        ->where('gadget_id', $gadget->id)
                        ->where('status', GadgetImei::STA_AVAILABLE)
                        ->lockForUpdate()
                        ->get();

                    $cocok = $imeis->pluck('imei')->all();
                    if (count($imeis) !== count($imeiTerpilih)) {
                        throw ValidationException::withMessages([
                            "items.*.imei" => 'Satu atau lebih IMEI tidak tersedia untuk "' . $gadget->nama_produk . '": ' . implode(', ', array_diff($imeiTerpilih, $cocok)),
                        ]);
                    }

                    foreach ($imeis as $imei) {
                        $imei->update([
                            'status' => GadgetImei::STA_SOLD,
                            'penjualan_item_id' => $itemBaris->id,
                            'sold_at' => now(),
                        ]);
                    }
                }
            }

            if ($tradeInValue > 0) {
                $desc = trim((string) ($data['trade_in_desc'] ?? 'Unit tukar tambah'));
                $nama = 'Trade-in: ' . ($desc ?: 'Unit tukar tambah');

                $unitMasuk = Gadget::create([
                    'nama_produk' => $nama,
                    'kategori' => 'Rekondisi',
                    'supplier' => ($data['customer'] ?: ($user?->name ?: 'Pelanggan')),
                    'deskripsi' => 'Unit bekas diterima via tukar tambah pada nota ' . $no
                        . '. Nilai taksiran: Rp ' . number_format($tradeInValue, 0, ',', '.')
                        . '. Menunggu pengecekan teknisi.',
                    'harga_beli' => $tradeInValue,
                    'harga_jual' => $tradeInValue,
                    'satuan' => 'unit',
                    'stock' => 0,
                    'stok_minimum' => 0,
                    'status' => 'Habis',
                    'is_published' => 0,
                    'is_featured' => 0,
                    'condition' => 'used',
                    'warranty_info' => 'Belum dicek',
                ]);

                StokService::adjust($unitMasuk, StokLog::TIPE_PENERIMAAN, 1, "Tukar tambah nota {$no}");

                if ($imeiMasuk = trim((string) ($data['trade_in_imei'] ?? ''))) {
                    GadgetImei::create([
                        'gadget_id' => $unitMasuk->id,
                        'imei' => $imeiMasuk,
                        'status' => GadgetImei::STA_AVAILABLE,
                        'masuk_via' => $no,
                        'masuk_at' => now(),
                        'notes' => 'Unit diterima via tukar tambah',
                    ]);
                }
            }

            $dasarPajak = $total - $diskon;
            $totalAkhir = round($dasarPajak + ($dasarPajak * $pajakPersen / 100), 2);

            $sisaBayar = round(max(0, $totalAkhir - $tradeInValue), 2);

            $dibayar = ($data['paid_amount'] !== null && $data['paid_amount'] !== '')
                ? (float) $data['paid_amount']
                : ($tradeInValue > 0 ? $sisaBayar : $totalAkhir);

            if ((in_array($metode, [Pembayaran::CASH, Pembayaran::TRADEIN], true) || $tradeInValue > 0) && ($dibayar + $tradeInValue + 0.001) < $totalAkhir) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Pembayaran (tunai + tukar tambah) kurang dari total tagihan.',
                ]);
            }

            $kembalian = round(max(0, $dibayar - $sisaBayar), 2);

            $penjualan->update([
                'total' => $totalAkhir,
                'paid_amount' => $dibayar,
                'change_amount' => $kembalian,
                'trade_in_value' => $tradeInValue,
                'trade_in_desc' => $data['trade_in_desc'] ?? null,
            ]);

            AuditLog::catat($user, 'tambah penjualan', $penjualan, [
                'no_invoice' => $no,
                'total' => $totalAkhir,
                'diskon' => $diskon,
                'ppn_percent' => $pajakPersen,
                'metode_bayar' => $metode,
                'dibayar' => $dibayar,
                'trade_in_value' => $tradeInValue,
                'jumlah_item' => count($data['items']),
                'tipe_pelanggan' => $tipe,
                'dropship' => $isDropship,
            ], $ip);

            return $penjualan->fresh();
        });
    }

    /**
     * Void / retur: kembalikan stok dan tandai status tanpa menghapus catatan.
     */
    public static function void(Penjualan $penjualan, ?User $user, ?string $ip, ?string $alasan = null): void
    {
        DB::transaction(function () use ($penjualan, $user, $ip, $alasan) {
            if ($penjualan->payment_status === 'void') {
                return;
            }

            foreach ($penjualan->items as $item) {
                StokService::adjust(
                    Gadget::findOrFail($item->gadget_id),
                    StokLog::TIPE_RETUR,
                    (int) $item->qty,
                    "Retur penjualan {$penjualan->no_invoice}"
                );

                // Kembalikan status IMEI yang tadi terjual menjadi available.
                GadgetImei::where('penjualan_item_id', $item->id)->update([
                    'status' => GadgetImei::STA_AVAILABLE,
                    'penjualan_item_id' => null,
                    'sold_at' => null,
                ]);
            }

            $penjualan->update([
                'payment_status' => 'void',
                'voided_at' => now(),
                'voided_by' => $user?->name ?: 'Sistem',
                'void_reason' => $alasan ?: 'Retur / batal penjualan',
            ]);

            AuditLog::catat($user, 'batal penjualan', $penjualan, [
                'no_invoice' => $penjualan->no_invoice,
                'total' => (float) $penjualan->total,
                'alasan' => $alasan ?: 'Retur / batal penjualan',
                'stok_dikembalikan' => true,
            ], $ip);
        });
    }
}