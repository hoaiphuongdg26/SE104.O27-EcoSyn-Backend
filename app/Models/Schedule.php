<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Schedule extends Model
{
    use HasFactory, UsesUuid, Filterable;

    protected $keyType = 'string';  // Chỉ định kiểu khóa chính là chuỗi
    public $incrementing = false;   // Không tự động tăng

    protected $fillable = [
        'staff_id',
        'route_id',
        'start_time',
        'end_time',
    ];
}
