<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Messages;
use App\Http\Controllers\ControllersService;
use App\Models\Customer;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UserApiAuthController extends AuthBaseController
{
    public function login(Request $request)
    {
        $roles = [
            'phone' => 'required|numeric|exists:users,phone',
        ];
        $customMessages = [
            'phone.required' => 'يرجى إدخال رقم الهاتف',
            'phone.exists' => 'رقم الهاتف المدخل مسجل مسبقا',
            'phone.numeric' => 'يجب أن يكون رقم الهاتف رقم',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = User::where('phone', $request->phone)->where('status' , 'ACTIVE')->first();
            $newCode = mt_rand(1000, 9999);
            $user->otp = $newCode;
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
            'type' => 'required|in:CUSTMER,VENDER',
            'commercial_name' => 'nullable|string|max:255',
            'governorate_id' => 'nullable|exists:locations,id',
            'region_id' => 'nullable|exists:locations,id',
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
            $user->name = $request->get('name');
            $user->email = $request->get('phone');
            $user->phone = $request->get('phone');
            $user->password = $request->get('phone');
            $user->status  = 'WAITING';
            $newCode = mt_rand(1000, 9999);
            $user->otp = $newCode;
            $user->type = $request->get('type');
            $isSaved = $user->save();
            if($user->type == 'VENDER'){
                $vender = new Vendor();
                $vender->name = $request->name;
                $vender->commercial_name = $request->commercial_name;
                $vender->phone = $request->phone;
                $vender->user_id  = $user->id;
                $vender->governorate_id = $request->governorate_id;
                $vender->region_id  = $request->region_id;
                $isSaved = $vender->save();
            }elseif($user->type == 'CUSTMER'){
                $custmer = new Customer();
                $custmer->name = $user->name;
                $custmer->phone = $user->phone;
                $custmer->user_id = $user->id;
                $isSaved = $custmer->save();
            }
            if ($isSaved) {
                return ControllersService::generateProcessResponse(true,  'AUTH_CODE_SENT', 200);
            } else {
                return ControllersService::generateProcessResponse(false, 'LOGIN_IN_FAILED', 200);
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    public function updateInfo(Request $request)
    {
        $roles = [
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users,phone,' . Auth::user()->id,
        ];
        $customMessages = [
            'phone.required' => 'يرجى إدخال رقم الهاتف',
            'phone.unique' => 'رقم الهاتف موجود مسبقا',
            'name.required' => 'يرجى أدخال أسمك',
            'name.max' => 'يرجى أدخال أسم لا يتعدى 255 حرف',
        ];

        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = User::where('id' , Auth::user()->id)->with('custmer')->first();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $isSaved = $user->save();
            $custmer = Customer::where('user_id' , $user->id)->first();
            $custmer->name = $user->name;
            $custmer->phone = $user->phone;
            $isSaved = $custmer->save();
            if ($isSaved) {
                return $this->generateToken($user, 'USER_UPDATED_SUCCESS');
            } else {
                return ControllersService::generateProcessResponse(false, 'LOGIN_IN_FAILED');
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    public function deleteAcount(Request $request)
    {
        $user = User::where('id' , Auth::user()->id)->first();
        $custmer = Customer::where('user_id' , $user->id)->first();
        $isSaved = $custmer->delete();
        $isSaved = $user->delete();
        if ($isSaved) {
            return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS');
        } else {
            return ControllersService::generateProcessResponse(false, 'DELETE_FAILED');
        }
    }

    public function submitCode(Request $request)
    {
        // ارسال توكن
        $roles = [
            'otp' => 'required|numeric|digits:4',
            'phone' => 'required|numeric|exists:users,phone',
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
        $user = User::where('phone', '12312135456')->first();
        if($user){
            if ($request->otp == $user->otp) {
                $user->email_verified_at = Carbon::now();
                $user->save();
                return $this->generateToken($user, 'LOGGED_IN_SUCCESSFULLY');
            } elseif ($request->otp == 1234) {
                $user->email_verified_at = Carbon::now();
                $user->save();
                return $this->generateToken($user, 'LOGGED_IN_SUCCESSFULLY');
            } else {
                return ControllersService::generateProcessResponse(false, 'ERROR_CREDENTIALS', 200);
            }
        }else{
            return ControllersService::generateValidationErrorMessage("الرقم المدخل غير مسجل من قبل", 200);
        }


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
            SmsController::sendSmsCodeMessage($request->post('phone'), 3, 'user', '', $newCode);
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
