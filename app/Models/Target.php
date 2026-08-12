<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = ['employee_id', 'period_month', 'visit_target', 'order_target', 'sales_target', 'collection_target'];

    public function employee() { return $this->belongsTo(Employee::class); }
}