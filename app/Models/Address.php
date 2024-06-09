<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory, UsesUuid;
    // Bỏ qua cài đặt tăng tự động cho khóa chính
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['unit_number', 'street_number', 'address_line', 'ward', 'district', 'city', 'province', 'country_name'];
    public function home(){
        return $this->belongsTo(Home::class,'id','id');
    }
    public function location(){
        return $this->hasOne(Location::class);
    }
}
