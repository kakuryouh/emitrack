<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    protected $table = 'fuels';

    public function logs(){
        return $this->hasMany(TravelLog::class, 'fuel_id');
    }
}
