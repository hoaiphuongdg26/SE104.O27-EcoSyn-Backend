<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory, UsesUuid;
    /**
     * Bỏ qua cài đặt tăng tự động cho khóa chính
     *
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $dispatchesEvents = [
        'deleting' => 'App\Events\VehicleDeleting',
    ];
    protected $fillable = ['license_plate', 'status', 'deleted'];
    public function staffs()
    {
        return $this->belongsToMany(User::class, 'staffs_vehicles', 'vehicle_id', 'staff_id');
    }
}
