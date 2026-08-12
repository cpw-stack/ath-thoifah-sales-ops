<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'visit_plan_id', 'employee_id', 'customer_id', 
        'check_in_at', 'check_in_lat', 'check_in_lng', 'distance_meters', 
        'check_in_status', 'check_in_photo', 'check_out_at', 'notes'
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function productChecks()
    {
        return $this->hasMany(VisitProductCheck::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    // TAMBAHKAN RELASI INI
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}