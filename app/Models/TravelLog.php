<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelLog extends Model
{
    protected $guarded = [];

    protected $table = 'travellogs';
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fuel(){
        return $this->belongsTo(Fuel::class, 'fuel_id');
    }

    public function publicTransports() {
        return $this->belongsToMany(PublicTransport::class, 
            'PublicTransportTravelLogs', 'log_id', 'public_transport_id')
            ->withPivot('leg_distance', 'leg_cost', 'emission');
    }
}
