<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Post extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    // Bỏ qua cài đặt tăng tự động cho khóa chính
    protected $keyType = 'string';
    public $incrementing = false;

    // Tạo UUID (bảo đảm không null)
    public static function booted()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
    protected $fillable = ['title', 'content', 'image_url', 'staff_id', 'deleted'];
}
