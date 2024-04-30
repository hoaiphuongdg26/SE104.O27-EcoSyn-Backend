<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IOT_Device extends Model
{
    use HasFactory;
    protected $table = 'iot_devices';
    protected $fillable = ['ip', 'air_val', 'left_status', 'right_status', 'status'];
}
