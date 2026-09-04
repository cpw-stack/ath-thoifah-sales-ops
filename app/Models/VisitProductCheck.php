<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitProductCheck extends Model
{
    protected $table = 'visit_product_checks';

    protected $fillable = [
        'visit_id',
        'product_id',
        'is_available',
        'stock_estimate',
        'notes',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}