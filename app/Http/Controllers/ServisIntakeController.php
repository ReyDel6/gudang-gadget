<?php

namespace App\Http\Controllers;

use App\Models\ServiceTicket;
use App\Models\StoreSetting;
use App\Models\User;
use App\Services\ServisService;
use Illuminate\Http\Request;

class ServisIntakeController extends Controller
{
    public function create()
    {
        $settings = (new StorefrontController)->settings();

        return view('store.ajukan-servis', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'device_brand' => ['required', 'string', 'max:50'],
            'device_model' => ['required', 'string', 'max:100'],
            'imei_or_serial' => ['nullable', 'string', 'max:100'],
            'completeness' => ['nullable', 'string', 'max:500'],
            'initial_condition' => ['nullable', 'string', 'max:1000'],
            'problem_description' => ['required', 'string', 'max:2000'],
        ]);

        $cashier = User::where('role', User::ROLE_ADMIN)->orderBy('id')->first()
            ?? User::first();

        $tiket = ServisService::buatTiket($cashier, $data, $request->ip());

        return redirect()->route('shop.intake.sukses', ['no' => $tiket->no_tiket]);
    }

    public function sukses(Request $request)
    {
        $noTiket = strtoupper(trim((string) $request->query('no', '')));

        $tiket = $noTiket !== ''
            ? ServiceTicket::with(['logs.user', 'technician'])->where('no_tiket', $noTiket)->first()
            : null;

        $settings = (new StorefrontController)->settings();

        return view('store.ajukan-servis-sukses', compact('tiket', 'settings'));
    }
}