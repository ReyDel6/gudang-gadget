<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\PublikOrder;
use App\Models\PublikOrderItem;
use App\Services\KeranjangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreOrderController extends Controller
{
    public function tambah(Request $request)
    {
        $data = $request->validate([
            'gadget_id' => ['required', 'integer'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $gadget = Gadget::publik()->find($data['gadget_id']);
        if (! $gadget) {
            return back()->withErrors(['gadget_id' => 'Produk tidak ditemukan atau belum dipublikasi.']);
        }

        $qty = (int) ($data['qty'] ?? 1);
        if ($qty > (int) $gadget->stock) {
            return back()->withErrors(['qty' => "Stok {$gadget->nama_produk} tersisa {$gadget->stock}."]);
        }

        KeranjangService::tambah($gadget->id, $qty);

        return back()->with('sukses_keranjang', "{$gadget->nama_produk} masuk keranjang.");
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'gadget_id' => ['required', 'integer'],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $qty = (int) $data['qty'];
        $aksi = (string) $request->input('aksi', '');

        if ($aksi === 'tambah' || $aksi === 'kurang') {
            $gadget = Gadget::publik()->find($data['gadget_id']);
            $batas = $gadget ? min(99, max(1, (int) $gadget->stock)) : 99;
            $qty = $aksi === 'tambah'
                ? min($batas, $qty + 1)
                : max(1, $qty - 1);
        }

        KeranjangService::set((int) $data['gadget_id'], $qty);

        return redirect()->route('shop.keranjang');
    }

    public function hapus(Request $request)
    {
        $data = $request->validate([
            'gadget_id' => ['required', 'integer'],
        ]);

        KeranjangService::hapus((int) $data['gadget_id']);

        return redirect()->route('shop.keranjang');
    }

    public function keranjang()
    {
        $baris = KeranjangService::baris();
        $settings = (new StorefrontController)->settings();
        $resellerMode = KeranjangService::sedangReseller();

        return view('store.keranjang', compact('baris', 'settings', 'resellerMode'));
    }

    public function checkout()
    {
        $baris = KeranjangService::baris();
        if ($baris->isEmpty()) {
            return redirect()->route('shop.keranjang');
        }

        $settings = (new StorefrontController)->settings();
        $resellerMode = KeranjangService::sedangReseller();
        $metode = $this->metodeTersedia($settings);
        $subtotal = $baris->sum('subtotal');

        return view('store.checkout', compact('baris', 'settings', 'resellerMode', 'metode', 'subtotal'));
    }

    public function store(Request $request)
    {
        $baris = KeranjangService::baris();
        if ($baris->isEmpty()) {
            return redirect()->route('shop.keranjang')
                ->withErrors(['keranjang' => 'Keranjang belanja masih kosong.']);
        }

        $settings = (new StorefrontController)->settings();
        $metodeTersedia = $this->metodeTersedia($settings);

        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'regex:/^[0-9+ ]{6,20}$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in($metodeTersedia)],
        ]);

        foreach ($baris as $barisItem) {
            if (! $barisItem['stok_cukup']) {
                return back()->withErrors([
                    'items' => 'Stok ' . $barisItem['gadget']->nama_produk . ' tidak mencukupi (sisa '
                        . $barisItem['gadget']->stock . '). Kurangi jumlah lalu coba lagi.',
                ])->withInput();
            }
        }

        $resellerId = KeranjangService::sedangReseller() ? auth()->id() : null;
        $subtotal = round($baris->sum('subtotal'), 2);

        $order = DB::transaction(function () use ($baris, $data, $resellerId, $subtotal) {
            $order = PublikOrder::create([
                'kode' => $this->buatKode(),
                'reseller_id' => $resellerId,
                'nama_pelanggan' => $data['nama_pelanggan'],
                'telepon' => $data['telepon'],
                'alamat' => $data['alamat'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'payment_method' => $data['payment_method'],
                'subtotal' => $subtotal,
                'status' => PublikOrder::STATUS_PENDING,
            ]);

            foreach ($baris as $item) {
                PublikOrderItem::create([
                    'order_id' => $order->id,
                    'gadget_id' => $item['gadget']->id,
                    'nama_produk' => $item['gadget']->nama_produk,
                    'harga_satuan' => $item['harga_satuan'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        KeranjangService::hapusSemua();

        return redirect()->route('shop.order.status', $order->kode)->with('sukses', 'Pesanan berhasil dibuat!');
    }

    public function status($kode)
    {
        $order = PublikOrder::with(['items', 'penjualan'])->where('kode', $kode)->firstOrFail();
        $settings = (new StorefrontController)->settings();

        return view('store.order-status', compact('order', 'settings'));
    }

    public function uploadBukti(Request $request, $kode)
    {
        $order = PublikOrder::where('kode', $kode)->firstOrFail();

        abort_unless($order->status === PublikOrder::STATUS_PENDING, 422, 'Order tidak lagi menunggu bukti.');
        abort_unless($order->payment_method === 'transfer', 422, 'Metode pembayaran bukan transfer.');
        abort_if($order->bukti_path, 422, 'Bukti transfer sudah diunggah.');

        $data = $request->validate([
            'bukti' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $order->update(['bukti_path' => $request->file('bukti')->store('bukti-transfer', 'public')]);

        return back()->with('sukses', 'Bukti transfer diterima. Admin akan memverifikasi pesanan Anda.');
    }

    protected function buatKode(): string
    {
        return DB::transaction(function () {
            $bas = 'ORD-' . date('Ymd');
            $jumlah = DB::table('publik_orders')->lockForUpdate()
                ->where('kode', 'like', "{$bas}%")->count();
            $kode = $bas . '-' . str_pad((string) ($jumlah + 1), 3, '0', STR_PAD_LEFT);

            while (DB::table('publik_orders')->where('kode', $kode)->exists()) {
                $jumlah++;
                $kode = $bas . '-' . str_pad((string) ($jumlah + 1), 3, '0', STR_PAD_LEFT);
            }

            return $kode;
        });
    }

    protected function metodeTersedia(array $settings): array
    {
        $metode = [];
        if (($settings['payment_transfer_enabled'] ?? '1') === '1') {
            $metode[] = 'transfer';
        }
        if (($settings['payment_cod_enabled'] ?? '1') === '1') {
            $metode[] = 'cod';
        }
        if (($settings['payment_store_enabled'] ?? '1') === '1') {
            $metode[] = 'store';
        }

        return $metode;
    }
}