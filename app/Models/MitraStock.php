<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraStock extends Model
{
    protected $table = 'mitra_stocks';

    protected $fillable = [
        'customer_id',
        'product_id',
        'qty_konsinyasi',
        'qty_lunas',
        'qty_piutang',
    ];

    // TAMBAHKAN RELASI INI
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}