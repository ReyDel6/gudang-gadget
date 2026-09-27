<?php

namespace App\Services;

use App\Models\Gadget;
use App\Models\StokLog;
use App\Models\StokOpname;
use App\Models\StokOpnameItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpnameService
{
    public static function buka(User $user, ?string $category = null, ?string $notes = null): StokOpname
    {
        return DB::transaction(function () use ($user, $category, $notes) {
            $opname = StokOpname::create([
                'no_invoice' => InvoiceService::buat('OPN', 'stock_opnames'),
                'auditor_id' => $user->id,
                'category_filter' => $category ?: null,
                'status' => StokOpname::ST_IN_PROGRESS,
                'total_system_items' => 0,
                'total_physical_items' => 0,
                'total_difference' => 0,
                'notes' => $notes ?: null,
                'started_at' => now(),
            ]);

            $produk = Gadget::query()
                ->when($category, fn ($q, $c) => $q->where('kategori', $c))
                ->orderBy('nama_produk')
                ->get(['id', 'nama_produk', 'sku', 'stock', 'kategori']);

            foreach ($produk as $p) {
                $opname->items()->create([
                    'gadget_id' => $p->id,
                    'system_stock' => (int) $p->stock,
                    'physical_stock' => null,
                    'difference' => null,
                ]);
            }

            $opname->update(['total_system_items' => (int) $produk->sum('stock')]);

            return $opname;
        });
    }

    public static function scan(StokOpname $opname, string $sku): array
    {
        if ($opname->status !== StokOpname::ST_IN_PROGRESS) {
            throw ValidationException::withMessages([
                'sku' => 'Opname ini sudah selesai/dibatalkan, tidak bisa di-scan lagi.',
            ]);
        }

        return DB::transaction(function () use ($opname, $sku) {
            $key = trim($sku);
            $gadget = Gadget::query()
                ->where('sku', $key)
                ->orWhere('id', ctype_digit($key) ? (int) $key : -1)
                ->first();

            if (! $gadget) {
                throw ValidationException::withMessages([
                    'sku' => "SKU \"{$key}\" tidak ditemukan.",
                ]);
            }

            $row = StokOpnameItem::where('stock_opname_id', $opname->id)
                ->where('gadget_id', $gadget->id)
                ->first();

            if (! $row) {
                $row = $opname->items()->create([
                    'gadget_id' => $gadget->id,
                    'system_stock' => (int) $gadget->stock,
                    'physical_stock' => 1,
                    'difference' => ((int) $gadget->stock) - 1,
                ]);
            } else {
                $row->update([
                    'physical_stock' => ((int) ($row->physical_stock ?? 0)) + 1,
                ]);
                $row->refresh();
                $selisih = ($row->physical_stock ?? 0) - (int) $row->system_stock;
                $row->update(['difference' => $selisih]);
                $row->refresh();
            }

            $totalFisik = (int) $opname->items()->whereNotNull('physical_stock')->sum('physical_stock');
            $totalSelisih = (int) $opname->items()->sum(DB::raw('COALESCE(difference, 0)'));

            $opname->update([
                'total_physical_items' => $totalFisik,
                'total_difference' => $totalSelisih,
            ]);

            return [
                'ok' => true,
                'id' => $row->id,
                'nama' => $gadget->nama_produk,
                'sku' => $gadget->sku,
                'system' => (int) $row->system_stock,
                'physical' => (int) ($row->physical_stock ?? 0),
                'difference' => (int) ($row->difference ?? 0),
                'total_physical' => $totalFisik,
                'total_difference' => $totalSelisih,
            ];
        });
    }

    public static function selesai(StokOpname $opname, ?string $notes = null): StokOpname
    {
        if ($opname->status !== StokOpname::ST_IN_PROGRESS) {
            throw ValidationException::withMessages(['opname' => 'Opname sudah ditutup.']);
        }

        return DB::transaction(function () use ($opname, $notes) {
            foreach ($opname->items as $row) {
                $gadget = Gadget::query()->find($row->gadget_id);
                if (! $gadget) {
                    continue;
                }
                $sistem = (int) $row->system_stock;
                // Item yang belum dihitung dianggap masih sesuai jumlah sistem
                // (bukan 0), sehingga tidak ada selisih & stok tidak terpotong.
                $fisik = $row->physical_stock !== null ? (int) $row->physical_stock : $sistem;
                $delta = $fisik - $sistem;

                $row->update([
                    'physical_stock' => $fisik,
                    'difference' => $delta,
                ]);

                StokService::adjust($gadget, StokLog::TIPE_OPNAME, $delta, "Opname {$opname->no_invoice}: fisik {$fisik} vs sistem {$sistem}.");
            }

            $opname->update([
                'status' => StokOpname::ST_COMPLETED,
                'total_system_items' => (int) $opname->items()->sum(DB::raw('COALESCE(system_stock, 0)')),
                'total_physical_items' => (int) $opname->items()->sum(DB::raw('COALESCE(physical_stock, 0)')),
                'total_difference' => (int) $opname->items()->sum(DB::raw('COALESCE(difference, 0)')),
                'notes' => $notes ?: $opname->notes,
                'completed_at' => now(),
            ]);

            return $opname;
        });
    }

    public static function batal(StokOpname $opname): void
    {
        if ($opname->status !== StokOpname::ST_IN_PROGRESS) {
            throw ValidationException::withMessages(['opname' => 'Hanya opname yang sedang berjalan yang bisa dibatalkan.']);
        }

        $opname->update(['status' => StokOpname::ST_CANCELLED]);
    }
}