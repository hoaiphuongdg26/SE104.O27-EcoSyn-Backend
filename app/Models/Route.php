<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Traits\UsesUuid;

// ...


class Route extends Model
{
    use HasFactory, Filterable, HasApiTokens, HasFactory, Notifiable, UsesUuid;

    protected $keyType = 'string';  
    public $incrementing = false;   
    
    protected $fillable = [
        'start_home',
        'end_home',
        'status_id'
    ];
    public function startHome()
    {
        return $this->belongsTo(Home::class, 'start_home');
    }

    public function endHome()
    {
        return $this->belongsTo(Home::class, 'end_home');
    }
}
