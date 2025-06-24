<?php

namespace App\Models\BookingModule\PNR\Master;

use App\Models\BookingModule\PNR\Detail\PnrDetail;
use Illuminate\Database\Eloquent\Model;

class PnrMaster extends Model
{
    protected $fillable = [
        'user_id', 'operator_id', 'parent_route', 'date', 
        'pnr', 'payment_status', 'pnr_status'
    ];

    public function details()
    {
        return $this->hasMany(PnrDetail::class, 'pnr_master_id');
    }
}
