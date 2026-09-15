<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GadgetFoto extends Model
{
    protected $table = 'gadget_fotos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = ['id', 'url'];

    public function gadget()
    {
        return $this->belongsTo(Gadget::class);
    }
}