<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VisitScheduleRequest extends Model
{
    protected $fillable = ['employee_id', 'customer_id', 'visit_date', 'status', 'reject_reason', 'approved_by', 'approved_at'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function approver() { return $this->belongsTo(Employee::class, 'approved_by'); }
}