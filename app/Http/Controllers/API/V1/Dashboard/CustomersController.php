<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\CustomerCollection;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countRow = $request->countRow;
        $customers = Customer::
        when($request->postingTime, function ($builder) use ($request) {
            $value = $request->postingTime;
            $weekAgo = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
            $monthAgo = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $yearAgo = Carbon::now()->startOfYear()->format('Y-m-d H:i:s');
            $last24Hours = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            if ($value == '24') {
                $builder->whereBetween('created_at', [$last24Hours, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'week') {
                $builder->whereBetween('created_at', [$weekAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'month') {
                $builder->whereBetween('created_at', [$monthAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'year') {
                $builder->whereBetween('created_at', [$yearAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            }
        })
        ->with('user' , 'governorate' , 'region')->withCount('orders')
        ->latest()->paginate($countRow ?? 15);
        return response()->json([
            'message' => 'تمت العمليه بنجاح',
            'code' => 200,
            'status' => true,
            'count' => $customers->total(),
            'data' => new CustomerCollection($customers),
            'pages' => [
                'current_page' => $customers->currentPage(),
                'total' => $customers->total(),
                'page_size' => $customers->perPage(),
                'next_page' => $customers->nextPageUrl(),
                'last_page' => $customers->lastPage(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $roles = [
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users',
            'type' => 'required|in:CUSTOMER,VENDOR',
            'governorate_id' => 'required|exists:locations,id',
            'region_id' => 'required|exists:locations,id',
        ];

        $customMessages = [
            'phone.required' => 'يرجى ادخال رقم الهاتف الخاص بك',
            'phone.unique' => 'هذا الرقم موجود مسبقا',
            'name.required' => 'يرجى ادخال إسم الشخصي الخاصة بك',
            'name.max' => 'يجب أن يكون إسمك أقل من 255 حرف',
            'governorate_id.exists' => 'لا توجد محافظة بهذا الأسم',
            'region_id.exists' => 'لا توجد منطقة بهذا الأسم',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if ($validator->fails()) {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('phone');
        $user->phone = $request->get('phone');
        $user->password = $request->get('phone');
        $newCode = mt_rand(1000, 9999);
        $user->otp = $newCode;
        $user->type = $request->get('type');
        $user->save();
        $customer = new Customer();
        $customer->name = $user->name;
        $customer->phone = $user->phone;
        $customer->user_id = $user->id;
        $customer->governorate_id = $request->governorate_id;
        $customer->region_id  = $request->region_id;
        $customer->save();
        $user = User::with('customer')->find($user->id);
        return parent::success($user , "تم العملية بنجاح");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::with('user' , 'governorate' , 'region')->withCount('orders')->find($id);

        return parent::success($customer , 'تمت العملية بنجاح');
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
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:customers,phone,' . $id,
            'type' => 'required|in:CUSTOMER,VENDOR',
            'governorate_id' => 'required|exists:locations,id',
            'region_id' => 'required|exists:locations,id',
        ];

        $customMessages = [
            'phone.required' => 'يرجى ادخال رقم الهاتف الخاص بك',
            'phone.unique' => 'هذا الرقم موجود مسبقا',
            'name.required' => 'يرجى ادخال إسم الشخصي الخاصة بك',
            'name.max' => 'يجب أن يكون إسمك أقل من 255 حرف',
            'governorate_id.exists' => 'لا توجد محافظة بهذا الأسم',
            'region_id.exists' => 'لا توجد منطقة بهذا الأسم',
        ];
        $validator = Validator::make($request->all(), $roles, $customMessages);
        if ($validator->fails()) {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }

        $customer = Customer::find($id);
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->governorate_id = $request->governorate_id;
        $customer->region_id  = $request->region_id;
        $customer->save();
        $user = User::find($customer->user_id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();
        $customer = Customer::with('user')->find($id);
        return parent::success($customer , "تم العملية بنجاح");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Customer::find($id)->delete();
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }

    public function status(Request $request , $id)
    {
        $validator = Validator($request->all(), [
            'status' => 'required|in:ACTIVE,INACTIVE',
        ], [
            'status.required' => 'يرجى أرسال الحالة',
            'status.in' => 'يرجى أختبار حالة بشكل صيحيح',
        ]);
        if (!$validator->fails()){
            $customer = User::with('customer')->find(Customer::find($id)->user_id);
            $customer->update(['status' => $request->status]);
            $customer = Customer::with('user')->find($id);
            return parent::success($customer , "تم العملية بنجاح");
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }
}
