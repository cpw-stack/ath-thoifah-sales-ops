<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesArea extends Model
{
    use SoftDeletes;

    protected $table = 'sales_areas';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];
}