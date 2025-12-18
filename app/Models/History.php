<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'user_id',
        'origin',
        'destination',
        'total_emission',
        'transportation_mode',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
