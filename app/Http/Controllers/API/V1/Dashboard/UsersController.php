<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\RoleHasUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = User::with('role_has_user.role.permission')->where('type' , 'USER')->get();
        $users = User::with('role_has_user.role')->where('type' , 'USER')->get();

        return parent::success($users , 'تمت العملية بنجاح');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $userRequest)
    {
        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $userRequest->get('name');
            $user->phone = $userRequest->get('phone');
            $user->email = $userRequest->get('email');
            $user->password = $userRequest->get('phone');
            $user->status  = 'ACTIVE';
            $user->otp = mt_rand(1000, 9999);
            $user->type = 'USER';
            $user->save();
            RoleHasUser::create([
                'user_id' => $user->id,
                'role_id' => $userRequest->role_id,
            ]);
        DB::commit();
        return ControllersService::generateProcessResponse(true,  'CREATE_SUCCESS', 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('role_has_user.role.permission')->where('type' , 'USER')->find($id);

        return parent::success($user , 'تمت العملية بنجاح');
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
        $roles = [
            'name' => 'required|max:255',
            'phone' => 'required|numeric|unique:users,phone,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ];
        $customMessages = [
            'name.required' => 'يرجى أدخال أسم المستخدم',
            'name.max' => 'لا يمكن لأسم المستخدم ان ابكون اكتر 255 حرف',
            'phone.required' => 'يرجى أدخال الهاتف',
            'phone.numeric' => 'لا يمكن للهاتف ان يكون نص',
            'phone.unique' => 'هذا الرقم موجود من قبل',
            'email.required' => 'يرجى أدخال ايميل',
            'email.email' =>  'يرجى أدخال ايميل',
            'email.unique' => 'هذه الايميل موجود مسبقا',
            'role_id.required' => 'يرجى أدخال المسمى الوظيفي',
            'role_id.exists' => 'لا يوجد مسمى بهذا الاسم',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if ($validator->fails()) {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $user->name = $request->get('name');
            $user->phone = $request->get('phone');
            $user->email = $request->get('email');
            $user->save();
            RoleHasUser::where('user_id' , $user->id)->update([
                'role_id' => $request->role_id,
            ]);
        DB::commit();
        return ControllersService::generateProcessResponse(true,  'UPDATE_SUCCESS', 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
