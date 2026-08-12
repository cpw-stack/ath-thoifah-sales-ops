<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'receivable_id', 'visit_id', 'employee_id', 'amount', 'payment_date', 'payment_method', 
        'payment_proof', 'notes', 'status'
    ];

    // TAMBAHKAN INI
    protected $casts = [
        'payment_date' => 'date',
        'status' => 'string'
    ];

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}