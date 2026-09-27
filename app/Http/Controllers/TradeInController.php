<?php

namespace App\Http\Controllers;

use App\Models\TradeInMaster;

class TradeInController extends Controller
{
    public function index()
    {
        $masters = TradeInMaster::aktif()->orderBy('brand')->orderBy('model_name')->orderBy('capacity')->get();

        $data = $masters->groupBy('brand')->map(function ($models, $brand) {
            return [
                'brand' => $brand,
                'models' => $models->map(fn ($m) => [
                    'id' => $m->id,
                    'model' => $m->model_name,
                    'capacity' => $m->capacity,
                    'a' => (float) $m->base_price_grade_a,
                    'b' => (float) $m->price_grade_b,
                    'c' => (float) $m->price_grade_c,
                    'd' => (float) $m->price_grade_d,
                ]),
            ];
        })->values();

        return view('store.trade-in', [
            'data' => $data,
            'settings' => (new StorefrontController)->settings(),
            'resellerMode' => (new StorefrontController)->resellerMode(),
        ]);
    }
}