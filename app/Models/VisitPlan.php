<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitPlan extends Model
{
    protected $fillable = ['employee_id', 'customer_id', 'visit_date', 'status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function visit()
    {
        return $this->hasOne(Visit::class);
    }
}