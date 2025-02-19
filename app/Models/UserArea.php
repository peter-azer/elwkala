<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserArea extends Model
{
    /** @use HasFactory<\Database\Factories\UserAreaFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'area_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function area(){
        return $this->belongsTo(Area::class);
        }
}
