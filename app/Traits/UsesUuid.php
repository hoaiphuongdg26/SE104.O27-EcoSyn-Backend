<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait UsesUuid
{
    // Tạo UUID (bảo đảm không null)
    public static function booted()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = $model->id ?? Str::uuid();
        });
    }
}
