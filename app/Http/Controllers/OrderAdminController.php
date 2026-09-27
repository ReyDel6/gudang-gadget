<?php

namespace App\Http\Controllers;

use App\Models\PublikOrder;
use App\Services\PenjualanService;
use App\Support\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = PublikOrder::withCount('items')->with('reseller');

        if ($status = $request->input('status')) {
            if (! in_array($status, [PublikOrder::STATUS_PENDING, PublikOrder::STATUS_CONFIRMED, PublikOrder::STATUS_CANCELLED], true)) {
                $status = null;
            }
        }
        if (! empty($status)) {
            $query->where('status', $status);
        }
        if ($cari = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode', 'like', "%{$cari}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$cari}%")
                    ->orWhere('telepon', 'like', "%{$cari}%");
            });
        }

        $orders = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $counts = [
            'pending' => PublikOrder::hanyaMenunggu()->count(),
            'confirmed' => PublikOrder::where('status', PublikOrder::STATUS_CONFIRMED)->count(),
            'cancelled' => PublikOrder::where('status', PublikOrder::STATUS_CANCELLED)->count(),
        ];

        return view('order.index', compact('orders', 'counts', 'status', 'cari'));
    }

    public function show($id)
    {
        $order = PublikOrder::with(['items', 'reseller', 'penjualan'])->findOrFail($id);

        return view('order.show', compact('order'));
    }

    public function konfirmasi(Request $request, $id)
    {
        $order = PublikOrder::with('items')->findOrFail($id);
        abort_unless($order->status === PublikOrder::STATUS_PENDING, 422, 'Order tidak dalam status menunggu.');

        $data = $request->validate([
            'ongkos_kirim' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'kurir' => ['nullable', 'string', 'max:100'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $items = $order->items->map(fn ($i) => [
            'gadget_id' => $i->gadget_id,
            'qty' => (int) $i->qty,
            'harga_jual' => round((float) $i->harga_satuan, 2),
        ])->all();

        $ongkir = (float) ($data['ongkos_kirim'] ?? 0);
        $total = round((float) $order->subtotal + $ongkir, 2);

        $isReseller = (bool) $order->reseller_id;
        $reseller = $order->reseller;
        $resellerProfil = $reseller?->reseller;

        $payload = [
            'customer' => $isReseller
                ? ($resellerProfil?->store_name ?: ($reseller?->name ?? 'Reseller'))
                : $order->nama_pelanggan,
            'customer_phone' => $isReseller
                ? ($resellerProfil?->whatsapp ?: $order->telepon)
                : $order->telepon,
            'customer_type' => $isReseller ? 'reseller' : 'retail',
            'is_dropship' => $isReseller,
            'sender_name' => $isReseller ? ($resellerProfil?->store_name ?: $order->nama_pelanggan) : null,
            'sender_phone' => $isReseller ? ($resellerProfil?->whatsapp ?: $order->telepon) : null,
            'recipient_name' => $order->nama_pelanggan,
            'recipient_address' => $order->alamat,
            'keterangan' => $isReseller
                ? 'Order publik ' . $order->kode . ($order->catatan ? ' — ' . $order->catatan : '')
                : 'Order publik ' . $order->kode
                    . ($order->alamat ? ' — Alamat kirim: ' . $order->alamat : '')
                    . ($order->catatan ? ' — ' . $order->catatan : ''),
            'items' => $items,
            'payment_method' => $order->payment_method === 'transfer'
                ? Pembayaran::TRANSFER
                : Pembayaran::CASH,
            'paid_amount' => $total,
            'payment_ref' => $order->kode,
        ];

        try {
            $penjualan = PenjualanService::buat($payload, Auth::user(), request()->ip());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['pesan' => 'Gagal membuat penjualan: ' . $e->getMessage()])->withInput();
        }

        $order->update([
            'status' => PublikOrder::STATUS_CONFIRMED,
            'penjualan_id' => $penjualan->id,
            'ongkos_kirim' => $ongkir,
            'kurir' => $data['kurir'] ?? null,
            'total' => $total,
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'confirmed_at' => now(),
        ]);

        return redirect()->route('order.show', $order->id)
            ->with('success', 'Order dikonfirmasi. Penjualan ' . $penjualan->no_invoice . ' dibuat, stok sudah terpotong.');
    }

    public function batal(Request $request, $id)
    {
        $order = PublikOrder::findOrFail($id);
        abort_unless($order->status === PublikOrder::STATUS_PENDING, 422, 'Hanya order menunggu yang bisa dibatalkan.');

        $order->update([
            'status' => PublikOrder::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        return redirect()->route('order.show', $order->id)
            ->with('success', 'Order dibatalkan.');
    }
}