<?php

namespace App\Models\BookingModule\PNR\Detail;

use App\Models\BookingModule\PNR\Master\PnrMaster;
use Illuminate\Database\Eloquent\Model;

class PnrDetail extends Model
{
    protected $fillable = [
        'pnr_master_id', 'pnr','gender', 'name', 'seat_no', 'seat_type'
    ];

    public function master()
    {
        return $this->belongsTo(PnrMaster::class, 'pnr_master_id');
    }
    
}
