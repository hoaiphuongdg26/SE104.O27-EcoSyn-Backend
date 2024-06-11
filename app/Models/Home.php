<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory, UsesUuid, Filterable;
    // Bỏ qua cài đặt tăng tự động cho khóa chính
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['customer_id'];
    public function address(){
        return $this->hasOne(Address::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
}
