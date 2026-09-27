<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Gadget;
use App\Models\Penjualan;
use App\Models\StokLog;
use App\Services\InvoiceService;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('items');

        if ($cari = trim((string) $request->input('search'))) {
            $query->where('no_invoice', 'like', "%{$cari}%")
                ->orWhere('customer', 'like', "%{$cari}%");
        }
        $query->periode($request->input('tanggal_awal'), $request->input('tanggal_akhir'));

        $penjualans = $query->latest('id')->paginate(15)->withQueryString();

        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $products = Gadget::orderBy('nama_produk')->get(['id', 'nama_produk', 'sku', 'harga_beli', 'harga_jual', 'stock', 'satuan']);

        return view('penjualan.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'customer' => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.gadget_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_jual' => ['nullable', 'numeric', 'min:0'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'pajak' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'items.required' => 'Minimal satu item penjualan.',
            'items.*.qty.min' => 'Jumlah item harus minimal 1.',
            'items.*.gadget_id.exists' => 'Produk yang dipilih tidak valid.',
        ]);

        $tanggal = $data['tanggal'] ?? now()->toDateString();
        $user = Auth::user();
        $diskon = (float) ($data['diskon'] ?? 0);
        $pajakPersen = (float) ($data['pajak'] ?? 0);

        try {
            $penjualan = DB::transaction(function () use ($data, $tanggal, $user, $request, $diskon, $pajakPersen) {
                $no = InvoiceService::buat('PJ', 'penjualans');

                $penjualan = Penjualan::create([
                    'no_invoice' => $no,
                    'tanggal' => $tanggal,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'customer' => $data['customer'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'total' => 0,
                    'diskon' => $diskon,
                    'pajak' => $pajakPersen,
                ]);

                $total = 0;
                foreach ($data['items'] as $item) {
                    $gadget = StokService::adjust(
                        Gadget::findOrFail($item['gadget_id']),
                        StokLog::TIPE_PENGELUARAN,
                        -((int) $item['qty']),
                        "Penjualan {$no}"
                    );

                    $hargaJual = (float) ($item['harga_jual'] ?? $gadget->harga_jual ?: $gadget->harga_beli);
                    $subtotal = round($hargaJual * (int) $item['qty'], 2);
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

                $penjualan->update(['total' => $totalAkhir]);

                AuditLog::catat($user, 'tambah penjualan', $penjualan, [
                    'no_invoice' => $no,
                    'total' => $totalAkhir,
                    'diskon' => $diskon,
                    'ppn_percent' => $pajakPersen,
                    'jumlah_item' => count($data['items']),
                ], $request->ip());

                return $penjualan;
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', "Penjualan {$penjualan->no_invoice} berhasil dicatat.");
    }

    public function show($id)
    {
        $penjualan = Penjualan::with('items.gadget')->findOrFail($id);

        return view('penjualan.show', compact('penjualan'));
    }

    public function cetak($id)
    {
        $penjualan = Penjualan::with('items')->findOrFail($id);

        return view('penjualan.print', compact('penjualan'));
    }

    public function destroy(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        DB::transaction(function () use ($penjualan, $request) {
            foreach ($penjualan->items as $item) {
                StokService::adjust(
                    Gadget::findOrFail($item->gadget_id),
                    StokLog::TIPE_PENYESUAIAN,
                    (int) $item->qty,
                    "Batal penjualan {$penjualan->no_invoice} (stok dikembalikan)"
                );
            }

            AuditLog::catat($request->user(), 'batal penjualan', null, [
                'no_invoice' => $penjualan->no_invoice,
                'total' => (float) $penjualan->total,
            ], $request->ip());

            $penjualan->delete();
        });

        return redirect()->route('penjualan.index')
            ->with('success', "Penjualan \"{$penjualan->no_invoice}\" dibatalkan dan stok dikembalikan.");
    }
}