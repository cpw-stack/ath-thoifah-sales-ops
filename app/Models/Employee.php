<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'user_id', 'employee_code', 'full_name', 'phone_number', 'sales_area_id', 
        'supervisor_id', 'status', 'birth_date', 'gender', 'id_card_number'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function salesArea()
    {
        return $this->belongsTo(SalesArea::class);
    }

    public function tasks() 
    { 
        return $this->hasMany(Task::class); 
    }

    public function target() 
    { 
        return $this->hasOne(Target::class); 
    }

    // Relasi untuk Sales Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}