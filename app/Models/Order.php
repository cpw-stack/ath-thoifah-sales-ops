<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = ['order_code', 'visit_id', 'customer_id', 'employee_id', 'total_amount', 
    'payment_type', 'delivery_date', 'notes', 'status'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // TAMBAHKAN RELASI INI
    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}