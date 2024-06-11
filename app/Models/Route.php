<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\UsesUuid;

// ...


class Route extends Model
{
    use HasFactory, HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'string';  
    public $incrementing = false;   

    protected $fillable = [
        'start_home',
        'end_home',
        'status_id',
    ];
    protected $primaryKey = 'id';
    public function startHome()
    {
        return $this->belongsTo(Home::class, 'start_home');
    }

    public function endHome()
    {
        return $this->belongsTo(Home::class, 'end_home');
    }
}
