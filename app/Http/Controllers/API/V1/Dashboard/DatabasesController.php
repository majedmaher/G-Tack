<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DatabasesController extends Controller
{
    public function backup(Request $request)
    {
        $file = Carbon::now()->format('Y-m-d-H-i-s') . '-mybackup.sql';
        Artisan::call('db:backup', [ 'file' => $file ]);
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
    }

    public function restore(Request $request)
    {
        $data = $request->all();
        $validator = Validator($data, [
            // 'file' => 'required|file',
        ]);
        if($validator->fails()){
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  200);
        }
        Artisan::call('db:restore', [ 'file' => $data['file'] ]);
        return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
    }

    public function empty()
    {
        Artisan::call('db:empty');
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
