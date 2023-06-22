<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\RoleHasPermission;
use App\Services\CreatedLog;
use Illuminate\Http\Request;
use Throwable;

class RoleController extends Controller
{
    public function index(Request $request) {
        $roles = Role::with('permission')->when($request->type , function ($q) use($request) {
            $q->where('type' , $request->type);
        })->get();
        return RoleResource::collection($roles)->additional(['message' => 'تمت العمليه بنجاح' , 'code' => 200 , 'status' => true]);
    }

    public function store(RoleRequest $roleRequest){
        try {
            $role = Role::create(['name' => $roleRequest->name , 'type' => $roleRequest->type]);
            foreach($roleRequest->permissions as $value){
                RoleHasPermission::create([
                    'role_id' => $role ->id,
                    'permission_id' => $value
                ]);
            }
            $role = Role::with('permission')->find($role->id);
            CreatedLog::handle('أضافة دور جديد');
            return parent::success($role , "تم العملية بنجاح");
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(RoleRequest $roleRequest , $id){
        try {
            $role = Role::find($id)->update(['name' => $roleRequest->name]);
            $permissions = RoleHasPermission::where('role_id' , $id)->whereNotIn('permission_id' , $roleRequest->permissions)->delete();
            foreach($roleRequest->permissions as $value){
                $permission = RoleHasPermission::where('role_id' , $id)->where('permission_id' , $value)->first();
                if(!$permission){
                    RoleHasPermission::create([
                        'role_id' => $id,
                        'permission_id' => $value
                    ]);
                }
            }
            $role = Role::with('permission')->find($id);
            CreatedLog::handle('تعديل دور');
            return parent::success($role , "تم العملية بنجاح");
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        Role::find($id)->delete();
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
