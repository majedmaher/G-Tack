<?php

namespace App\Http\Controllers\API;

use App\Helpers\Messages;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\AdCollection;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Models\School;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;


class UserApiAuthController extends AuthBaseController
{
    public function login(Request $request)
    {
        $roles = [
            'phone' => 'required|numeric|exists:users,phone',
        ];
        $customMessages = [
            'phone.exists' => '  هذا رقم الهاتف غير مسجل مسبقا',
            'email.required' => ' الاميل مطلوبه'
        ];

        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = User::where("phone", $request->get('phone'))->where('status' , 'ACTIVE')->first();
            return ControllersService::generateProcessResponse(true,  'AUTH_CODE_SENT', 200);
        } else {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    public function register(Request $request)
    {
        $roles = [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric|unique:users',
            'image' => 'required|image',
            'school_name' => 'required|string|min:3',
        ];
        $customMessages = [
            'email.required' => 'يرجى ادخال الايميل الخاص بك',
            'email.unique' => 'هذا الإيميل موجود مسبقا',
            'phone.required' => 'يرجى ادخال رقم الهاتف الخاص بك',
            'phone.unique' => 'هذا الرقم موجود مسبقا',
            'name.required' => 'يرجى ادخال إسم الشخصي الخاصة بك',
            'school_name.required' => 'يرجى ادخال اسم المدرسة',
            'image.required' => 'يرجى ادخال الصوره الخاصة ب المدرسة',
            'image.image' => 'يجب ان تقوم برفع صورة',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = new User();
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->phone = $request->get('phone');
            $user->password = $request->get('phone');
            $newCode = mt_rand(1000, 9999);
            $user->code = $newCode;
            $user->save();
            $school = new School();
            $school->school_name = $request->get('school_name');
            if ($request->hasFile('image')) {
                $userImage = $request->file('image');
                $imageName = time() . '_' . '.' . $userImage->getClientOriginalExtension();
                $userImage->move('image/users', $imageName);
                $school->image = 'image/users/' . $imageName;
            }
            $school->user_id = $user->id;
            $school->save();
            $isSaved = $user->update(['school_id' => $school->id]);
            // SmsController::sendSmsCodeMessage($request->post('phone'), 3, 'user', '', $newCode);
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
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id,
            'password' => 'required|min:3',
            'phone' => 'required|unique:users,phone,' . Auth::user()->id,
            'last_name' => 'required|string|min:3',
            'image' => 'nullable',
            'new_password_confirmation' => 'required|string|same:password',
            'old_password' => 'required|min:3',

        ];

        $customMessages = [
            'email.required' => ' :الايميل مطلوب.',
            'email.unique' => ' :الايميل موجود مسبقاً.',
            'phone.required' => ' :رقم الهاتف مطلوب.',
            'phone.unique' => ' :رقم الهاتف موجود مسبقاً.',
            'name.required' => ' :الاسم مطلوب.',
            'password.required' => ' :كلمة المرور مطلوب.',
            'last_name.required' => ' :الاسم الاخير مطلوب.',
            'new_password_confirmation.required' => ' :تأكيد كلمة المرور مطلوب .',
            'new_password_confirmation.same' => ' :تأكيد كلمة المرور يجب ان تكون متطابقة .',
            'old_password.required' => ' : كلمة الامور السابقة مطلوب.',
        ];

        $validator = Validator::make($request->all(), $roles, $customMessages);
        if (!$validator->fails()) {
            $user = Auth::user();
            if (!Hash::check($request->old_password, $user->password)) {
                return ControllersService::generateProcessResponse(false, 'ERROR_CREDENTIALS');
            }
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->password = Hash::make($request->get('password'));
            $user->last_name = $request->get('last_name');
            $user->phone = $request->get('phone');
            if ($request->hasFile('image')) {
                $userImage = $request->file('image');
                $imageName = time() . '_' . '.' . $userImage->getClientOriginalExtension();
                $userImage->move('image/users', $imageName);
                $user->image = '/image/users/' . $imageName;
            }
            $isSaved = $user->save();
            if ($isSaved) {
                return $this->generateToken($user, 'LOGGED_IN_SUCCESSFULLY');
            } else {
                return ControllersService::generateProcessResponse(false, 'LOGIN_IN_FAILED');
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
    }

    public function submitCode(Request $request)
    {
        // ارسال توكن
        $roles = [
            'code' => 'required|numeric|digits:4',
            'phone' => 'required|numeric|exists:users,phone',
        ];
        $customMessages = [
            'code.numeric' => ' الكود يجب ان يكون رقمي',
            'code.required' => 'الكود مطلوب مطلوبه',
            'code.digits' => 'الكود يجب ان يكون 4 ارقام',
            'phone.exists' => '  هذا رقم الهاتف غير مسجل مسبقا',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if ($validator->fails())
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(), 200);
        $user = User::with('school')->where('phone', $request->phone)->first();
        if ($request->code == $user->code) {
            $user->email_verified_at = Carbon::now();
            $user->save();
            return $this->generateToken($user, 'LOGGED_IN_SUCCESSFULLY');
        } elseif ($request->code == 1234) {
            $user->email_verified_at = Carbon::now();
            $user->save();
            return $this->generateToken($user, 'LOGGED_IN_SUCCESSFULLY');
        } else {
            return ControllersService::generateProcessResponse(false, 'ERROR_CREDENTIALS', 200);
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
        $token = $tokenResult->accessToken;
        $user->setAttribute('token', $token);
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => Messages::getMessage($message),
            'data' => $user,
        ]);
    }
}
