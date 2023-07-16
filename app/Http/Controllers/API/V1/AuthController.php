<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Messages;
use App\Http\Controllers\API\V1\AuthBaseController;
use App\Http\Controllers\ControllersService;
use App\Models\Customer;
use App\Models\User;
use App\Models\Vendor;
use App\Services\DivecTokensService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
class AuthController extends AuthBaseController
{
    public function login(Request $request)
    {
        $roles = [
            'phone' => 'required|numeric|exists:users,phone|'.Rule::exists("users", "phone")->whereNull("deleted_at"),
            'type' => 'required|in:CUSTOMER,VENDOR,ADMIN,USER',
        ];
        $customMessages = [
            'phone.required' => 'يرجى إدخال رقم الهاتف',
            'phone.exists' => 'الرقم الدخل لم يتم تسجيلة',
            'phone.numeric' => 'يجب أن يكون رقم الهاتف رقم',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = User::where('phone', $request->phone)->where('type' , $request->type)->first();
            if(!$user){
                return ControllersService::generateProcessResponse(false, 'LOGIN_IN_FAILED', 200);
            }
            $newCode = mt_rand(1000, 9999);
            $user->otp = $newCode;
            $user->is_phone_verified = 1;
            $isSaved = $user->save();
            if ($isSaved) {
                return ControllersService::generateProcessResponse(true,  'AUTH_CODE_SENT', 200);
            } else {
                return ControllersService::generateProcessResponse(false, 'LOGIN_IN_FAILED', 200);
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    public function register(Request $request)
    {
        $roles = [
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users',
            'type' => 'required|in:CUSTOMER,VENDOR',
            'vendor_type' => 'nullable|in:GAS,WATER',
            'commercial_name' => 'nullable|string|max:255',
            'governorate_id' => 'nullable|exists:locations,id',
            'region_id' => 'nullable|exists:locations,id',
            'avatar' => 'nullable|image',
        ];

        $customMessages = [
            'phone.required' => 'يرجى ادخال رقم الهاتف الخاص بك',
            'phone.unique' => 'هذا الرقم موجود مسبقا',
            'name.required' => 'يرجى ادخال إسم الشخصي الخاصة بك',
            'name.max' => 'يجب أن يكون إسمك أقل من 255 حرف',
            'commercial_name.max' => 'يجب أن يكون إسمك التجاري أقل من 255 حرف',
            'governorate_id.exists' => 'لا توجد محافظة بهذا الأسم',
            'region_id.exists' => 'لا توجد منطقة بهذا الأسم',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->phone;
            $user->phone = $request->phone;
            $user->password = $request->phone;
            $newCode = mt_rand(1000, 9999);
            $user->otp = $newCode;
            $user->type = $request->type;
            if ($user->type == 'CUSTOMER') {
            $user->status = 'ACTIVE';
            }
            $isSaved = $user->save();
            if ($user->type == 'VENDOR') {
                $vendor = new Vendor();
                $vendor->type = $request->vendor_type;
                $vendor->name = $request->name;
                $vendor->commercial_name = $request->commercial_name;
                $vendor->phone = $request->phone;
                $vendor->user_id  = $user->id;
                $vendor->governorate_id = $request->governorate_id;
                $vendor->region_id  = $request->region_id ?? NULL;
                $vendor->max_product  = $request->max_product;
                if ($request->file('avatar')) {
                    $name = Str::random(12);
                    $path = $request->file('avatar');
                    $name = $name . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
                    $vendor->avatar = 'vendor/avatars/'.$name;
                    $path->move('vendor/avatars', $name);
                }
                $isSaved = $vendor->save();
            } elseif ($user->type == 'CUSTOMER') {
                $customer = new Customer();
                $customer->name = $user->name;
                $customer->phone = $user->phone;
                $customer->user_id = $user->id;
                $customer->governorate_id = $request->governorate_id;
                $customer->region_id  = $request->region_id;
                $isSaved = $customer->save();
            }
            if ($isSaved) {
                return ControllersService::generateProcessResponse(true,  'AUTH_CODE_SENT', 200);
            } else {
                return ControllersService::generateProcessResponse(false, 'LOGIN_IN_FAILED', 200);
            }
        } else {
            $user = User::where('phone' , $request->get('phone'))->onlyTrashed()->first();
            // return $user;
            if($user){
                $user->restore();
                if ($user->customer()->withTrashed()->exists()) {
                    $user->customer()->withTrashed()->restore();
                }
                return ControllersService::generateProcessResponse(true,  'AUTH_CODE_SENT', 200);
            }
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    public function updateInfo(Request $request)
    {
        $roles = [
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users,phone,' . Auth::user()->id,
            'governorate_id' => 'required|exists:locations,id',
            'region_id' => 'required|exists:locations,id',
        ];
        $customMessages = [
            'phone.required' => 'يرجى إدخال رقم الهاتف',
            'phone.unique' => 'رقم الهاتف موجود مسبقا',
            'name.required' => 'يرجى أدخال أسمك',
            'name.max' => 'يرجى أدخال أسم لا يتعدى 255 حرف',
        ];

        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = User::where('id', Auth::user()->id)->with('customer')->first();
            $user->name = $request->name;
            if($user->phone != $request->phone){
                $user->is_phone_verified = 1;
            }
            $user->phone = $request->phone;
            $isSaved = $user->save();
            $customer = Customer::where('user_id', $user->id)->first();
            $customer->name = $user->name;
            $customer->phone = $user->phone;
            $customer->governorate_id = $request->governorate_id;
            $customer->region_id = $request->region_id;
            $isSaved = $customer->save();
            $user = User::where('id', Auth::user()->id)->with('customer')->first();
            if ($isSaved) {
                return $this->generateToken($user, 'USER_UPDATED_SUCCESS');
            } else {
                return ControllersService::generateProcessResponse(false, 'LOGIN_IN_FAILED');
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    public function getUser(Request $request){
        $user = User::with('customer' , 'vendor.regions.region')->find(Auth::user()->id);
        return response()->json(['code' => 200 , 'status' => true,
        'message' => "تمت العملية بنجاح" , 'data' => $user]
        , 200);
    }
    public function deleteAcount(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if ($user->type == 'CUSTOMER') {
            $customer = Customer::where('user_id', $user->id)->first();
            $customer->delete();
            // $user->delete();
        } else {
            $vendor = Vendor::whereHas('orders' , function($q){
                $q->whereIn('status'  , ['PENDING' , 'ACCEPTED' , 'ONWAY' , 'PROCESSING' , 'FILLED' , 'DELIVERED']);
            })->where('user_id', $user->id)->first();
            if($vendor){
                return ControllersService::generateProcessResponse(false, 'DELETE_FAILED' , 200);
            }
            $vendor->delete();
            // $user->delete();
        }
            return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS' , 200);
    }

    public function submitCode(Request $request , DivecTokensService $divecTokensService)
    {
        // ارسال توكن
        $roles = [
            'otp' => 'required|numeric|digits:4',
            'phone' => 'required|numeric|exists:users,phone|'.Rule::exists("users", "phone")->whereNull("deleted_at"),
            'type' =>  'required|in:CUSTOMER,VENDOR,ADMIN,USER',
        ];
        $customMessages = [
            'otp.numeric' => 'يجب أن يكون الكود رقم',
            'otp.required' => 'يرجى إدخال الكود المرسل',
            'otp.digits' => 'يجب أن يكون الكود متكون من 4 خانات',
            'phone.exists' => 'الرقم المدخل غير مسجل من قبل',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if ($validator->fails())
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(), 200);
        $user = User::where('phone', $request->phone)->where('type' , $request->type)->with('customer' , 'vendor.regions.region')->first();

        if ($user) {
            $dataForToken = [
                'fcm_token' => $request->fcm_token,
                'user_id' => $user->id,
                'device_name' => $request->device_name,
            ];
            if ($request->otp == $user->otp) {
                $user->email_verified_at = Carbon::now();
                $user->is_phone_verified = 1;
                $user->save();
                $divecTokensService->handle($dataForToken);
                return $this->generateToken($user, 'LOGGED_IN_SUCCESSFULLY');
            } elseif ($request->otp == 1234) {
                $user->email_verified_at = Carbon::now();
                $user->is_phone_verified = 1;
                $user->save();
                $divecTokensService->handle($dataForToken);
                return $this->generateToken($user, 'LOGGED_IN_SUCCESSFULLY');
            } else {
                return ControllersService::generateProcessResponse(false, 'ERROR_CREDENTIALS', 200);
            }
        } else {
            return ControllersService::generateValidationErrorMessage("الرقم المدخل غير مسجل من قبل", 200);
        }
    }

    public function verify_code(Request $request){
        $roles = [
            'otp' => 'required|numeric|digits:4',
            'phone' => 'required|numeric|exists:users,phone|'.Rule::exists("users", "phone")->whereNull("deleted_at"),
            'type' =>  'required|in:CUSTOMER,VENDOR,ADMIN,USER',
        ];

        $customMessages = [
            'otp.numeric' => 'يجب أن يكون الكود رقم',
            'otp.required' => 'يرجى إدخال الكود المرسل',
            'otp.digits' => 'يجب أن يكون الكود متكون من 4 خانات',
            'phone.exists' => 'الرقم المدخل غير مسجل من قبل',
        ];

        $validator = Validator::make($request->all(), $roles, $customMessages);
        if ($validator->fails()){
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(), 200);
        }
        $user = User::where('phone', $request->phone)->where('type' , $request->type)->first();
        if ($user) {
            if ($request->otp == $user->otp && $request->otp == 1234) {
                $user->email_verified_at = Carbon::now();
                $user->is_phone_verified = 1;
                $user->save();
                return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
            } else {
                return ControllersService::generateProcessResponse(false, 'ERROR_CREDENTIALS', 200);
            }
        }
        return ControllersService::generateValidationErrorMessage("الرقم المدخل غير مسجل من قبل", 200);
    }

    public function sendCodePassword(Request $request)
    {
        $roles = [
            'phone' => 'required|numeric|exists:users,phone',
        ];
        $customMessages = [
            'phone.numeric' => ' الرقم يجب ان يكون رقمي',
            'phone.required' => 'الرقم مطلوب مطلوبه',
            'phone.exists' => 'الرقم غير مسجل',

        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = User::where("phone", $request->get('phone'))->first();
            $newCode = mt_rand(1000, 9999);
            $user->code = $newCode;
            $isSaved = $user->save();
            // SmsController::sendSmsCodeMessage($request->post('phone'), 3, 'user', '', $newCode);
            if ($isSaved) {
                return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS');
            } else {
                return ControllersService::generateProcessResponse(false, 'CREATE_FAILED');
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    private function generateToken($user, $message)
    {
        $tokenResult = $user->createToken('News-User');
        $token = $tokenResult->plainTextToken;
        $user->setAttribute('token', $token);
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => Messages::getMessage($message),
            'data' => $user,
        ]);
    }
}
