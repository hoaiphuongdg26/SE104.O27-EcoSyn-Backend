<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IOT_Device extends Model
{
    use HasFactory, UsesUuid;
    // Bỏ qua cài đặt tăng tự động cho khóa chính
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'iot_devices';
    protected $fillable = ['ip', 'air_val', 'left_status', 'right_status', 'status'];
}
