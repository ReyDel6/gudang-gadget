<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Gadget;
use App\Models\ServiceStatusLog;
use App\Models\ServiceTicket;
use App\Models\ServiceTicketItem;
use App\Models\StokLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServisService
{
    public static function buatKode(): string
    {
        $bas = 'SRV-' . now()->format('Ymd');
        $jumlah = DB::table('service_tickets')->lockForUpdate()->where('no_tiket', 'like', "{$bas}%")->count();

        do {
            $no = $bas . '-' . str_pad((string) (++$jumlah), 3, '0', STR_PAD_LEFT);
        } while (DB::table('service_tickets')->where('no_tiket', $no)->exists());

        return $no;
    }

    public static function buatTiket(User $user, array $data, ?string $ip = null): ServiceTicket
    {
        $tiket = ServiceTicket::create([
            'no_tiket' => self::buatKode(),
            'parent_id' => $data['parent_id'] ?? null,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_address' => $data['customer_address'] ?? null,
            'device_brand' => $data['device_brand'],
            'device_model' => $data['device_model'],
            'imei_or_serial' => $data['imei_or_serial'] ?? null,
            'passcode' => $data['passcode'] ?? null,
            'completeness' => $data['completeness'] ?? null,
            'initial_condition' => $data['initial_condition'] ?? null,
            'problem_description' => $data['problem_description'],
            'technician_notes' => $data['technician_notes'] ?? null,
            'technician_id' => $data['technician_id'] ?? null,
            'cashier_id' => $user->id,
            'status' => ServiceTicket::STATUS_BARU,
            'down_payment' => (float) ($data['down_payment'] ?? 0),
            'warranty_days' => (int) ($data['warranty_days'] ?? 30),
            'received_at' => now(),
        ]);

        self::catatLog($tiket, $user, ServiceTicket::STATUS_BARU, 'Tiket servis diterima oleh frontdesk.');
        AuditLog::catat($user, 'tambah tiket servis', $tiket, [
            'no_tiket' => $tiket->no_tiket,
            'pelanggan' => $tiket->customer_name,
            'unit' => $tiket->device_brand . ' ' . $tiket->device_model,
            'status' => $tiket->status,
        ], $ip);

        return $tiket->fresh();
    }

    public static function tambahItem(ServiceTicket $tiket, User $user, array $data, ?string $ip = null): ServiceTicketItem
    {
        if (in_array($tiket->status, [ServiceTicket::STATUS_DIAMBIL, ServiceTicket::STATUS_BATAL])) {
            throw ValidationException::withMessages(['item' => 'Tiket sudah selesai/dibatalkan, tidak dapat menambah item.']);
        }

        $tipe = $data['item_type'];
        $qty = max(1, (int) ($data['quantity'] ?? 1));
        $gadget = null;

        if ($tipe === ServiceTicketItem::TIPE_SPAREPART) {
            $gadget = Gadget::findOrFail($data['gadget_id']);
            StokService::adjust(
                $gadget,
                StokLog::TIPE_PENGELUARAN,
                -$qty,
                "Pemakaian Servis No #{$tiket->no_tiket}"
            );
            $itemName = ($data['item_name'] ?? '') !== '' ? $data['item_name'] : $gadget->nama_produk;
            $cost = (float) $gadget->harga_beli;
            $sell = array_key_exists('sell_price', $data) && $data['sell_price'] !== '' && $data['sell_price'] !== null
                ? (float) $data['sell_price']
                : (float) $gadget->harga_aktif;
        } else {
            $itemName = trim((string) $data['item_name']);
            if ($itemName === '' && ! empty($data['service_name'])) {
                $itemName = trim((string) $data['service_name']);
            }
            if ($itemName === '') {
                throw ValidationException::withMessages(['item_name' => 'Nama jasa wajib diisi.']);
            }
            $cost = 0;
            $sell = array_key_exists('sell_price', $data) && $data['sell_price'] !== '' && $data['sell_price'] !== null
                ? (float) $data['sell_price']
                : (float) ($data['service_fee'] ?? 0);
        }

        $subtotal = round($sell * $qty, 2);

        $item = $tiket->items()->create([
            'gadget_id' => $gadget?->id,
            'item_type' => $tipe,
            'item_name' => $itemName,
            'cost_price' => round($cost, 2),
            'sell_price' => round($sell, 2),
            'quantity' => $qty,
            'subtotal' => $subtotal,
        ]);

        self::hitungUlang($tiket);
        self::catatLog($tiket, $user, $tipe === ServiceTicketItem::TIPE_SPAREPART ? 'item' : 'jasa', "Ditambahkan: {$itemName} x{$qty} — Rp " . number_format($subtotal, 0, ',', '.'));
        AuditLog::catat($user, 'tambah item servis', $item, [
            'no_tiket' => $tiket->no_tiket,
            'item' => $itemName,
            'qty' => $qty,
            'harga' => $subtotal,
        ], $ip);

        return $item->fresh();
    }

    public static function hapusItem(ServiceTicket $tiket, User $user, ServiceTicketItem $item, ?string $ip = null): void
    {
        if (in_array($tiket->status, [ServiceTicket::STATUS_DIAMBIL, ServiceTicket::STATUS_BATAL])) {
            throw ValidationException::withMessages(['item' => 'Tiket sudah selesai/dibatalkan, tidak dapat menghapus item.']);
        }

        if ($item->item_type === ServiceTicketItem::TIPE_SPAREPART && $item->gadget_id) {
            try {
                StokService::adjust(
                    $item->gadget,
                    StokLog::TIPE_PENGELUARAN,
                    (int) $item->quantity,
                    "Pembatalan item servis No #{$tiket->no_tiket}"
                );
            } catch (\Throwable $e) {
                //
            }
        }

        $nama = $item->item_name;
        $item->delete();
        self::hitungUlang($tiket);
        self::catatLog($tiket, $user, 'item', "Dihapus: {$nama}");
        AuditLog::catat($user, 'hapus item servis', $tiket, ['no_tiket' => $tiket->no_tiket, 'item' => $nama], $ip);
    }

    public static function ubahStatus(ServiceTicket $tiket, User $user, string $status, ?string $notes = null, ?int $technicianId = null, ?string $ip = null): void
    {
        if (! isset(ServiceTicket::STATUS[$status])) {
            throw ValidationException::withMessages(['status' => 'Status tidak valid.']);
        }

        if (in_array($tiket->status, [ServiceTicket::STATUS_DIAMBIL, ServiceTicket::STATUS_BATAL])) {
            throw ValidationException::withMessages(['status' => 'Tiket sudah selesai/dibatalkan, status tidak dapat diubah.']);
        }

        $tiket->status = $status;
        $tiket->technician_notes = $notes !== null ? $notes : $tiket->technician_notes;
        if ($technicianId) {
            $tiket->technician_id = $technicianId;
        }
        if ($status === ServiceTicket::STATUS_SIAP && ! $tiket->completed_at) {
            $tiket->completed_at = now();
        }
        if ($status === ServiceTicket::STATUS_BATAL && ! $tiket->cancelled_at) {
            $tiket->cancelled_at = now();
        }
        $tiket->save();

        self::catatLog($tiket, $user, $status, $notes);
        AuditLog::catat($user, 'ubah status servis', $tiket, ['no_tiket' => $tiket->no_tiket, 'status' => $status, 'catatan' => $notes], $ip);
    }

    public static function simpanQc(ServiceTicket $tiket, User $user, array $checklist, ?string $catatan = null, ?string $ip = null): void
    {
        $valid = array_intersect(array_keys(ServiceTicket::QC_ITEMS), array_values($checklist));
        $tiket->qc_checklist = array_values($valid);
        $tiket->save();

        self::catatLog($tiket, $user, 'qc', $catatan ?: 'Quality check diperbarui.');
        AuditLog::catat($user, 'qc servis', $tiket, ['no_tiket' => $tiket->no_tiket, 'qc' => $valid], $ip);
    }

    public static function pelunasan(ServiceTicket $tiket, User $user, string $metode, float $dibayar, ?string $ref = null, ?string $ip = null): ServiceTicket
    {
        if (! in_array($metode, ServiceTicket::METODE_PELUNASAN)) {
            throw ValidationException::withMessages(['payment_method' => 'Metode pembayaran tidak valid.']);
        }

        DB::transaction(function () use ($tiket, $user, $metode, $dibayar, $ref, $ip) {
            $sisa = (float) $tiket->remaining_cost;

            if ($tiket->status === ServiceTicket::STATUS_DIAMBIL) {
                throw ValidationException::withMessages(['lunas' => 'Tiket ini sudah dilunasi.']);
            }
            if ($dibayar < $sisa) {
                throw ValidationException::withMessages(['paid_amount' => 'Nominal yang dibayarkan kurang dari sisa tagihan.']);
            }

            $tiket->forceFill([
                'payment_method' => $metode,
                'paid_amount' => round($dibayar, 2),
                'payment_ref' => $ref,
                'paid_at' => now(),
                'status' => ServiceTicket::STATUS_DIAMBIL,
                'remaining_cost' => 0,
                'delivered_at' => now(),
                'warranty_until' => now()->addDays((int) $tiket->warranty_days)->toDateString(),
            ])->save();

            self::catatLog($tiket, $user, ServiceTicket::STATUS_DIAMBIL, 'Unit diambil & tagihan lunas.');
            AuditLog::catat($user, 'lunas servis', $tiket, [
                'no_tiket' => $tiket->no_tiket,
                'metode' => $metode,
                'dibayar' => $dibayar,
                'garansi_sampai' => $tiket->warranty_until?->toDateString(),
            ], $ip);
        });

        return $tiket->fresh();
    }

    public static function buatGaransi(ServiceTicket $induk, User $user, array $data, ?string $ip = null): ServiceTicket
    {
        return DB::transaction(function () use ($induk, $user, $data, $ip) {
            $tiket = ServiceTicket::create([
                'no_tiket' => self::buatKode(),
                'parent_id' => $induk->id,
                'customer_name' => $data['customer_name'] ?? $induk->customer_name,
                'customer_phone' => $data['customer_phone'] ?? $induk->customer_phone,
                'customer_address' => $induk->customer_address,
                'device_brand' => $data['device_brand'] ?? $induk->device_brand,
                'device_model' => $data['device_model'] ?? $induk->device_model,
                'imei_or_serial' => $data['imei_or_serial'] ?? $induk->imei_or_serial,
                'passcode' => $induk->passcode,
                'completeness' => $induk->completeness,
                'initial_condition' => $induk->initial_condition,
                'problem_description' => $data['problem_description'],
                'technician_id' => $data['technician_id'] ?? $induk->technician_id,
                'cashier_id' => $user->id,
                'status' => ServiceTicket::STATUS_BARU,
                'down_payment' => 0,
                'warranty_days' => (int) ($data['warranty_days'] ?? 30),
                'received_at' => now(),
            ]);

            self::catatLog($tiket, $user, ServiceTicket::STATUS_BARU, 'Tiket garansi (re-work) dari tiket ' . $induk->no_tiket . ' — jasa Rp 0.');
            AuditLog::catat($user, 'tiket garansi servis', $tiket, ['no_tiket' => $tiket->no_tiket, 'induk' => $induk->no_tiket], $ip);

            return $tiket;
        });
    }

    public static function hitungUlang(ServiceTicket $tiket): void
    {
        $jasa = (float) $tiket->jasaItems()->sum('subtotal');
        $spare = (float) $tiket->sparepartItems()->sum('subtotal');

        $tiket->service_fee = round($jasa, 2);
        $tiket->sparepart_fee = round($spare, 2);
        $tiket->total_cost = round($jasa + $spare, 2);

        if ($tiket->status !== ServiceTicket::STATUS_DIAMBIL) {
            $tiket->remaining_cost = max(0, round((float) $tiket->total_cost - (float) $tiket->down_payment, 2));
        }

        $tiket->save();
    }

    protected static function catatLog(ServiceTicket $tiket, User $user, string $status, ?string $notes): void
    {
        ServiceStatusLog::create([
            'service_ticket_id' => $tiket->id,
            'user_id' => $user->id,
            'status' => $status,
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }
}