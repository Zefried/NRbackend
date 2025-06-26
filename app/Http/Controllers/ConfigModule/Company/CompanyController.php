<?php

namespace App\Http\Controllers\ConfigModule\Company;

use App\Http\Controllers\Controller;
use App\Models\ConfigModule\Company\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function addCompanyInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'operator_id'   => 'nullable',
                'company_name'  => 'nullable|string',
                'ac_status'     => 'nullable|boolean',
                'office_address'=> 'nullable|string',
                'no_of_buses'   => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'validation error',
                    'errors' => $validator->errors()
                ]);
            }

            $data = CompanyInfo::create($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Company info added successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong in server',
                'err' => $e->getMessage()
            ]);
        }
    }

}
