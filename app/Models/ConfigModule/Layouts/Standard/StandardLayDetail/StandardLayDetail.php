<?php

namespace App\Models\ConfigModule\Layouts\Standard\StandardLayDetail;

use App\Models\ConfigModule\Layouts\Standard\StandardLayMaster\StandardLayMaster;
use Illuminate\Database\Eloquent\Model;

class StandardLayDetail extends Model
{
    protected $fillable = [
        'layout_id',
        'type',       // seater, sleeper, upper, lower
        'row',
        'col'
    ];

    public function standardLayMaster(){
        return $this->belongsTo(StandardLayMaster::class);
    }
}
