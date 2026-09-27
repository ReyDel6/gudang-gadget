<?php

namespace App\Http\Controllers;

use App\Models\GadgetImei;
use App\Models\ServiceTicket;
use Illuminate\Http\Request;

class GadgetImeiController extends Controller
{
    public function index(Request $request)
    {
        $query = GadgetImei::with('gadget')->latest('id');

        if ($cari = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($cari) {
                $q->where('imei', 'like', "%{$cari}%")
                    ->orWhere('serial_number', 'like', "%{$cari}%")
                    ->orWhereHas('gadget', fn ($g) => $g->where('nama_produk', 'like', "%{$cari}%")
                        ->orWhere('sku', 'like', "%{$cari}%"));
            });
        }

        $query->status($request->input('status'));

        $imeis = $query->paginate(25)->withQueryString();

        return view('imei.index', compact('imeis'));
    }

    public function show($id)
    {
        $imei = GadgetImei::with(['gadget', 'penjualanItem.penjualan', 'penjualanItem.gadget'])->findOrFail($id);

        $servis = ServiceTicket::where('imei_or_serial', $imei->imei)->orWhere(function ($q) use ($imei) {
            $q->whereNotNull('imei')->where('imei', $imei->imei);
        })->latest('id')->get();

        return view('imei.show', compact('imei', 'servis'));
    }
}