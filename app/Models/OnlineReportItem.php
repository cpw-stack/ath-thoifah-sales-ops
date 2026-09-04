<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineReportItem extends Model
{
    protected $fillable = [
        'online_report_id',
        'product_id',
        'qty',
        'price',
        'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}