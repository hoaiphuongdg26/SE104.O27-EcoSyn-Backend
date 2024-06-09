<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
