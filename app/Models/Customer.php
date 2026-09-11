<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'customer_code',
        'name',
        'owner_name',
        'phone_number',
        'address',
        'latitude',
        'longitude',
        'credit_limit',
        'credit_terms_days',
        'status',
        'discount',
        'discount_status',
    ];

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function discounts()
    {
        return $this->hasMany(CustomerStockDiscount::class);
    }

    public function stocks()
    {
        return $this->hasMany(MitraStock::class);
    }
}