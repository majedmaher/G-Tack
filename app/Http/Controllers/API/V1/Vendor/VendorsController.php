<?php

namespace App\Http\Controllers\API\V1\Vendor;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users,phone,' . Auth::user()->id,
            'commercial_name' => 'required|string|max:255',
            'governorate_id' => 'nullable|exists:locations,id',
            'region_id' => 'nullable|exists:locations,id',
            'avatar' => 'nullable|image',
        ], [
            'phone.required' => __('يرجى ادخال رقم الهاتف الخاص بك'),
            'phone.unique' => 'لا يمكن أستخدام هذا الرقم',
            'name.required' => 'يرجى ادخال إسم الشخصي الخاصة بك',
            'name.max' => 'يجب أن يكون إسمك أقل من 255 حرف',
            'commercial_name.max' => 'يجب أن يكون إسمك التجاري أقل من 255 حرف',
            'governorate_id.exists' => 'لا توجد محافظة بهذا الأسم',
            'region_id.exists' => 'لا توجد منطقة بهذا الأسم',
        ]);

        if (!$validator->fails()) {
            $user = User::find(Auth::user()->id)->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => $request->phone,
            ]);
            $vendor = Vendor::find(Auth::user()->vendor->id);
            $avatar = NULL;
            if ($request->file('avatar')) {
                $name = Str::random(12);
                $path = $request->file('avatar');
                $name = $name . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
                $avatar = 'vendor/avatars/' . $name;
                $path->move('vendor/avatars', $name);
            }
            $vendor->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'commercial_name' => $request->commercial_name,
                'governorate_id' => $request->governorate_id ?? Vendor::find(Auth::user()->vendor->id)->governorate_id,
                'region_id' => $request->region_id ?? NULL,
                'max_product' => $request->max_product,
            ]);
            if ($avatar) {
                $vendor->update([
                    'avatar' => $avatar,
                ]);
            }
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => Messages::getMessage('UPDATE_SUCCESS'),
                'data' => User::where('id', Auth::user()->id)->with('vendor')->first(),
            ]);
        }
        return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        $vendor = Vendor::find($id)->update(['active' => $request->status]);
        return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
    }
}
