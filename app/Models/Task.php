<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'employee_id', 'customer_id', 'title', 'description', 'attachment', 'priority', 
        'due_date', 'status'
    ];

    // TAMBAHKAN INI
    protected $casts = [
        'due_date' => 'date',
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}