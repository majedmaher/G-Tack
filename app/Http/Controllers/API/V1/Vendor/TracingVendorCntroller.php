<?php

namespace App\Http\Controllers\API\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Services\PusherService;
use Illuminate\Http\Request;
use Throwable;

class TracingVendorCntroller extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tracking(Request $request, PusherService $pusherService)
    {
        $data = $request->all();
        try {
            $pusher = $pusherService->handle($data);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage(),], 500);
        }
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
    }

    public function authPusher(Request $request, PusherService $pusherService){
        $data = $request->all();
        try {
            return $pusherService->auth($data);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage(),], 500);
        }
    }
}
