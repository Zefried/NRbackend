<?php

namespace App\Models\ConfigModule\Layouts\Standard\StandardLayMaster;

use App\Models\ConfigModule\Layouts\Standard\StandardLayDetail\StandardLayDetail;
use Illuminate\Database\Eloquent\Model;

class StandardLayMaster extends Model
{
    protected $fillable = [
        'operator_id',
        'seater',
        'sleeper',
        'double_sleeper'
    ];

    public function standardLayDetail()
    {
        return $this->hasMany(StandardLayDetail::class, 'layout_id');
    }
}
