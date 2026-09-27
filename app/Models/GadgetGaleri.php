<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GadgetGaleri extends Model
{
    protected $table = 'gadget_galeri';

    protected $fillable = ['gadget_id', 'url', 'label', 'sort_order'];

    public function gadget(): BelongsTo
    {
        return $this->belongsTo(Gadget::class, 'gadget_id');
    }

    public function getUrlPublicAttribute(): string
    {
        if ($this->url && file_exists(public_path('storage/' . $this->url))) {
            return asset('storage/' . $this->url);
        }

        return asset('storage/thumbnails/no-image.jpg');
    }
}