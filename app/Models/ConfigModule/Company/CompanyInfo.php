<?php

namespace App\Models\ConfigModule\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $fillable = [
        'operator_id',
        'company_name',
        'ac_status',
        'office_address',
        'no_of_buses',
    ];
}
