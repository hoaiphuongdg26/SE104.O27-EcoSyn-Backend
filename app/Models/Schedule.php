<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Route;
use App\Models\Staff;

class Schedule extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';  // Chỉ định kiểu khóa chính là chuỗi
    public $incrementing = false;   // Không tự động tăng

    protected $fillable = [
        'staff_id',
        'route_id',
        'start_time',
        'end_time',
    ];
}
