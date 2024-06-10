<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;
    protected $keyType = 'string';  
    public $incrementing = false;   

    protected $fillable = [
        'route_id',
        'start_time',
        'end_time',
    ];
}
