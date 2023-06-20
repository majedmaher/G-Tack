<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\NotificationCollection;
use App\Models\User;
use App\Notifications\SendNotificationForAllUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications;
        return (new NotificationCollection($notifications))->additional(['message' => 'تمت العملية بنجاح']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        $notification->markAsRead();
        return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
    }

    public function send_notifcation(Request $request)
    {
        $data = $request->all();
        if($request->type == 'CUSTOMER'){
            $data['topic'] = 'gtack';
            $users = User::where('type' , 'CUSTOMER')->get();
        }else{
            $data['topic'] = 'gtackVendor';
            $users = User::where('type' , 'VENDOR')->get();
        }
        Notification::send($users, new SendNotificationForAllUsers($data));
    }

}
