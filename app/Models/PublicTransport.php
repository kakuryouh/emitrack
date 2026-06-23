<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicTransport extends Model
{
    protected $table = 'publictransports';

    public function logs(){
        return $this->belongsToMany(TravelLog::class);
    }
}
