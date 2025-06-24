<?php

namespace App\Http\Controllers\ConfigModule\Layouts\StandardLayout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ConfigModule\Layouts\Standard\StandardLayMaster\StandardLayMaster;

class StandardLayoutController extends Controller
{
    public function resource(Request $request)
    {
        $type = $request->type;

        if ($type === 'store') {
            return $this->store($request);
        }

        // if ($type === 'view') {
        //     return $this->view($request);
        // }

        // if ($type === 'search') {
        //     return $this->search($request);
        // }

        // if ($type === 'update') {
        //     return $this->update($request);
        // }

        // if ($type === 'disable') {
        //     return $this->disable($request);
        // }

        // if ($type === 'delete') {
        //     return $this->delete($request);
        // }

        return response()->json(['status' => false, 'message' => 'Invalid type'], 400);
    }


    private function store($request)
    {
        try {
            $validated = $request->validate([
                'operator_id'     => 'integer',
                'seater'          => 'boolean',
                'sleeper'         => 'boolean',
                'doubleSleeper'   => 'boolean',
                'data'            => 'required|array',
                'data.*.type'     => 'string|in:seater,sleeper,upper,lower',
                'data.*.row'      => 'integer|min:1',
                'data.*.col'      => 'integer|min:1',
            ]);

            $layout = StandardLayMaster::updateOrCreate(
                ['operator_id' => $request->get('operator_id')],
                [
                    'seater'        => $request->get('seater', false),
                    'sleeper'       => $request->get('sleeper', false),
                    'doubleSleeper' => $request->get('doubleSleeper', false),
                ]
            );

            // delete old layout details before adding new
            $layout->standardLayDetail()->delete();

            foreach ($validated['data'] as $entry) {
                $layout->standardLayDetail()->create([
                    'type' => $entry['type'],
                    'row'  => $entry['row'],
                    'col'  => $entry['col'],
                ]);
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Layout stored successfully',
                'data'    => $layout->load('standardLayDetail')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'something went wrong in server',
                'err'     => $e->getMessage()
            ]);
        }
    }








}
