<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GadgetTierPrice extends Model
{
    protected $table = 'gadget_tier_prices';

    protected $fillable = [
        'gadget_id',
        'tier_name',
        'min_qty',
        'max_qty',
        'price',
    ];

    protected $casts = [
        'min_qty' => 'integer',
        'max_qty' => 'integer',
        'price' => 'float',
    ];

    public function gadget(): BelongsTo
    {
        return $this->belongsTo(Gadget::class, 'gadget_id');
    }
}