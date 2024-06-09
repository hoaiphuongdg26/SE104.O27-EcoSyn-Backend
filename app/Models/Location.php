<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory, UsesUuid;
    // Bỏ qua cài đặt tăng tự động cho khóa chính
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['longitude', 'latitude', 'id'];
    public function address(){
        return $this->belongsTo(Address::class);
    }
}
