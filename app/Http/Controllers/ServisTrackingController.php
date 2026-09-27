<?php

namespace App\Http\Controllers;

use App\Models\ServiceTicket;
use Illuminate\Http\Request;

class ServisTrackingController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $result = null;
        $error = null;

        if ($q !== '') {
            $kode = strtoupper($q);
            $hp = preg_replace('/[^0-9]/', '', $q);

            $result = ServiceTicket::with(['items', 'logs.user', 'technician'])
                ->where(function ($b) use ($kode, $hp) {
                    if ($kode !== '') {
                        $b->orWhere('no_tiket', 'like', "%{$kode}%");
                    }
                    if ($hp !== '') {
                        $b->orWhereRaw('REGEXP_REPLACE(customer_phone, "[^0-9]", "") = ?', [$hp]);
                    }
                })
                ->latest('id')
                ->first();

            if (! $result) {
                $error = "Tiket servis dengan No. \"{$q}\" tidak ditemukan. Mohon periksa kembali nomor Anda.";
            }
        }

        $settings = (new StorefrontController)->settings();

        return view('store.tracking-service', compact('result', 'error', 'q', 'settings'));
    }
}