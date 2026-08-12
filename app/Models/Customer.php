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
    ];

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }
}