<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    protected $fillable = ['customer_id', 'reference_code', 'total_amount', 'paid_amount', 'due_date', 'status'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    // Accessor untuk mendapatkan sisa tagihan
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }
}