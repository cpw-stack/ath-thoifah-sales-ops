<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineReport extends Model
{
    protected $fillable = ['employee_id', 'report_date', 'start_time', 'end_time', 'total_amount', 'notes'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function items()
    {
        return $this->hasMany(OnlineReportItem::class);
    }
}