<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory, UsesUuid, Filterable;
    // Bỏ qua cài đặt tăng tự động cho khóa chính
    protected $keyType = 'string';
    public $incrementing = false;
    public $fillable = ['customer_id', 'description', 'status', 'deleted'];
}
