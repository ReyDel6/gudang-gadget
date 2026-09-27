<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Gadget;
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
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'pajak' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_method' => ['nullable', 'string', Rule::in(array_keys(Pembayaran::METODE))],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_ref' => ['nullable', 'string', 'max:100'],
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
        $tanggal = $data['tanggal'] ?? now()->toDateString();
        $tipe = $data['customer_type'] ?? 'retail';
        $isDropship = ! empty($data['is_dropship']) && filter_var($data['is_dropship'], FILTER_VALIDATE_BOOL);

        return DB::transaction(function () use ($data, $user, $ip, $shiftId, $diskon, $pajakPersen, $metode, $tanggal, $tipe, $isDropship) {
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

                // FR-3.2: tipe transaksi Mitra Reseller mengunci harga khusus mitra
                // (tarif partai terendah) tanpa autotier lanjutan.
                if ($tipe === 'reseller') {
                    $hargaJual = $gadget->harga_mitra;
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

                $penjualan->items()->create([
                    'gadget_id' => $gadget->id,
                    'nama_produk' => $gadget->nama_produk,
                    'harga_beli' => $gadget->harga_beli,
                    'harga_jual' => $hargaJual,
                    'qty' => (int) $item['qty'],
                    'subtotal' => $subtotal,
                ]);
            }

            $dasarPajak = $total - $diskon;
            $totalAkhir = round($dasarPajak + ($dasarPajak * $pajakPersen / 100), 2);

            $dibayar = ($data['paid_amount'] !== null && $data['paid_amount'] !== '')
                ? (float) $data['paid_amount']
                : $totalAkhir;

            if ($metode === Pembayaran::CASH && ($dibayar + 0.001) < $totalAkhir) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Uang yang dibayarkan kurang dari total tagihan.',
                ]);
            }

            $kembalian = round(max(0, $dibayar - $totalAkhir), 2);

            $penjualan->update([
                'total' => $totalAkhir,
                'paid_amount' => $dibayar,
                'change_amount' => $kembalian,
            ]);

            AuditLog::catat($user, 'tambah penjualan', $penjualan, [
                'no_invoice' => $no,
                'total' => $totalAkhir,
                'diskon' => $diskon,
                'ppn_percent' => $pajakPersen,
                'metode_bayar' => $metode,
                'dibayar' => $dibayar,
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