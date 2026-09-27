<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Gadget;
use App\Models\Pembelian;
use App\Models\PriceHistory;
use App\Models\StokLog;
use App\Services\InvoiceService;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with('items');

        if ($cari = trim((string) $request->input('search'))) {
            $query->where('no_invoice', 'like', "%{$cari}%")
                ->orWhere('supplier', 'like', "%{$cari}%");
        }
        $query->periode($request->input('tanggal_awal'), $request->input('tanggal_akhir'));

        $pembelians = $query->latest('id')->paginate(15)->withQueryString();

        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        $products = Gadget::orderBy('nama_produk')->get(['id', 'nama_produk', 'sku', 'harga_beli', 'harga_jual', 'stock', 'satuan']);
        $supplierList = \App\Models\Supplier::orderBy('nama')->pluck('nama')->all();

        return view('pembelian.create', compact('products', 'supplierList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'supplier' => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.gadget_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_beli' => ['nullable', 'numeric', 'min:0'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'pajak' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'items.required' => 'Minimal satu item pembelian.',
            'items.*.qty.min' => 'Jumlah item harus minimal 1.',
            'items.*.gadget_id.exists' => 'Produk yang dipilih tidak valid.',
        ]);

        $tanggal = $data['tanggal'] ?? now()->toDateString();
        $user = Auth::user();
        $diskon = (float) ($data['diskon'] ?? 0);
        $pajakPersen = (float) ($data['pajak'] ?? 0);

        try {
            $pembelian = DB::transaction(function () use ($data, $tanggal, $user, $request, $diskon, $pajakPersen) {
                $no = InvoiceService::buat('PB', 'pembelians');

                $pembelian = Pembelian::create([
                    'no_invoice' => $no,
                    'tanggal' => $tanggal,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'supplier' => $data['supplier'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'total' => 0,
                    'diskon' => $diskon,
                    'pajak' => $pajakPersen,
                ]);

                $total = 0;
                foreach ($data['items'] as $item) {
                    $gadget = StokService::adjust(
                        Gadget::findOrFail($item['gadget_id']),
                        StokLog::TIPE_PENERIMAAN,
                        (int) $item['qty'],
                        "Pembelian {$no}"
                    );

                    $hargaBeli = (float) ($item['harga_beli'] ?? $gadget->harga_beli);
                    $subtotal = round($hargaBeli * (int) $item['qty'], 2);
                    $total += $subtotal;

                    if ($hargaBeli != (float) $gadget->harga_beli) {
                        $hargaLama = (float) $gadget->harga_beli;
                        $gadget->update(['harga_beli' => $hargaBeli]);
                        PriceHistory::catat($gadget, $hargaLama, $hargaBeli, "Pembelian {$no}");
                    }

                    $pembelian->items()->create([
                        'gadget_id' => $gadget->id,
                        'nama_produk' => $gadget->nama_produk,
                        'harga_beli' => $hargaBeli,
                        'qty' => (int) $item['qty'],
                        'subtotal' => $subtotal,
                    ]);
                }

                $dasarPajak = $total - $diskon;
                $totalAkhir = round($dasarPajak + ($dasarPajak * $pajakPersen / 100), 2);

                $pembelian->update(['total' => $totalAkhir]);

                AuditLog::catat($user, 'tambah pembelian', $pembelian, [
                    'no_invoice' => $no,
                    'total' => $totalAkhir,
                    'diskon' => $diskon,
                    'ppn_percent' => $pajakPersen,
                    'jumlah_item' => count($data['items']),
                ], $request->ip());

                return $pembelian;
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('pembelian.show', $pembelian->id)
            ->with('success', "Pembelian {$pembelian->no_invoice} berhasil dicatat.");
    }

    public function show($id)
    {
        $pembelian = Pembelian::with('items.gadget')->findOrFail($id);

        return view('pembelian.show', compact('pembelian'));
    }

    public function cetak($id)
    {
        $pembelian = Pembelian::with('items')->findOrFail($id);

        return view('pembelian.print', compact('pembelian'));
    }

    public function destroy(Request $request, $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        DB::transaction(function () use ($pembelian, $request) {
            foreach ($pembelian->items as $item) {
                StokService::adjust(
                    Gadget::findOrFail($item->gadget_id),
                    StokLog::TIPE_PENYESUAIAN,
                    -((int) $item->qty),
                    "Batal pembelian {$pembelian->no_invoice} (stok dikurangi)"
                );
            }

            AuditLog::catat($request->user(), 'batal pembelian', null, [
                'no_invoice' => $pembelian->no_invoice,
                'total' => (float) $pembelian->total,
            ], $request->ip());

            $pembelian->delete();
        });

        return redirect()->route('pembelian.index')
            ->with('success', "Pembelian \"{$pembelian->no_invoice}\" dibatalkan dan stok dikurangi.");
    }
}