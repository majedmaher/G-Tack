<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthBaseController extends Controller
{

    public function user(Request $request)
    {
        return ControllersService::generateObjectSuccessResponse($request->user(), 'Success');
    }

    public function updateActivityStatus(Request $request)
    {
        $request->validate(['status' => 'required|string|in:Online,Offline']);

        $user = $request->user();
        $user->activity_status = $request->get('status');
        $isSaved = $user->save();
        return ControllersService::generateProcessResponse($isSaved, $isSaved ? 'USER_UPDATED_SUCCESS' : 'USER_UPDATED_FAILED');
    }

    protected function revokePreviousTokens($clientId, $userId)
    {
        DB::table('oauth_access_tokens')
            ->where('client_id', $clientId)
            ->where('user_id', $userId)
            ->update([
                'revoked' => true
            ]);
    }

    public function refreshFcmToken(Request $request)
    {
        $request->request->add([
            'fcm_token' => request()->header('fcm_token')
        ]);
        $request->validate(['fcm_token' => 'required|string']);

        $user = $request->user();
        $user->fcm_token = request()->header('fcm_token');
        $isSaved = $user->save();
        return ControllersService::generateProcessResponse($isSaved, $isSaved ? 'USER_UPDATED_SUCCESS' : 'USER_UPDATED_FAILED');
    }

    public function refreshSelectedAppLanguage(Request $request)
    {
        $request->request->add([
            'lang' => request()->header('lang')
        ]);
        $request->validate(['lang' => 'required|string|in:en,ar']);

        $user = $request->user();
        $user->app_language = request()->header('lang');
        $isSaved = $user->save();
        return ControllersService::generateProcessResponse($isSaved, $isSaved ? 'USER_UPDATED_SUCCESS' : 'USER_UPDATED_FAILED');
    }

    public function refreshSelectedAppType(Request $request)
    {
        $request->request->add([
            'mobile_type' => request()->header('mobile_type')
        ]);
        $request->validate(['mobile_type' => 'required|string|in:android,ios']);

        $user = $request->user();
        $user->app_language = request()->header('mobile_type');
        $isSaved = $user->save();
        return ControllersService::generateProcessResponse($isSaved, $isSaved ? 'USER_UPDATED_SUCCESS' : 'USER_UPDATED_FAILED');
    }
    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    // public function logoutadmin(Request $request)
    // {
    //     $request->user('admin_api')->token()->revoke();
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Successfully logged out'
    //     ]);
    // }
    // public function logoutsupplier(Request $request)
    // {
    //     $request->user('supplier_api')->token()->revoke();
    //     dd(123);
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Successfully logged out'
    //     ]);
    // }
}
