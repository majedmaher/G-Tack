<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\VendorCollection;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRegions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $show = $request->show;
        $countRow = $request->countRow;
        $vendors = Vendor::
        when($show == 'new', function($q) use($show){
            $q->whereHas('user' , function($qu) use($show) {
                $qu->where('status' , 'WAITING');
            });
        })
        ->when($show == 'old', function($q) use($show){
            $q->whereHas('user' , function($qu) use($show) {
                $qu->where('status' , 'ACTIVE');
            });
        })
        ->when($request->governorate, function($q) use($request){
                $q->where('governorate_id' , $request->governorate);
        })
        ->when($request->region, function($q) use($request){
            $q->where('region_id' , $request->region);
        })
        ->when($request->type, function($q) use($request){
            $q->where('type' , $request->type);
        })
        ->when($request->postingTime, function ($builder) use ($request) {
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
        ->with('governorate' , 'region' , 'user' , 'attachments.document')
        ->withCount('reviews')
        ->withSum('reviews' , 'rate')
        ->withSum('orders' , 'time')
        ->withCount('orders')
        ->withAvg('orders' , 'time')
        ->latest()->paginate($countRow ?? 15);

        return response()->json([
            'message' => 'تمت العمليه بنجاح',
            'code' => 200,
            'status' => true,
            'count' => $vendors->total(),
            'data' => new VendorCollection($vendors),
            'pages' => [
                'current_page' => $vendors->currentPage(),
                'total' => $vendors->total(),
                'page_size' => $vendors->perPage(),
                'next_page' => $vendors->nextPageUrl(),
                'last_page' => $vendors->lastPage(),
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
            'vendor_type' => 'required|in:GAS,WATER',
            'commercial_name' => 'nullable|string|max:255',
            'governorate_id' => 'nullable|exists:locations,id',
            'region_ids' => 'nullable|array|exists:locations,id',
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
        if ($validator->fails()) {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->phone;
        $user->phone = $request->phone;
        $user->password = $request->phone;
        $user->otp = mt_rand(1000, 9999);
        $user->type = $request->type;
        $user->save();
        $vendor = new Vendor();
        $vendor->type = $request->vendor_type;
        $vendor->name = $request->name;
        $vendor->commercial_name = $request->commercial_name;
        $vendor->phone = $request->phone;
        $vendor->user_id  = $user->id;
        $vendor->governorate_id = $request->governorate_id;
        $vendor->save();
        foreach ($request->region_ids as $value){
            VendorRegions::create([
                'vendor_id' => $vendor->id,
                'region_id' => $value,
            ]);
        }
        $user = User::with('vendor.regions.region' , 'vendor.governorate')->find($user->id);
        return parent::success($user , "تم العملية بنجاح");
        return ControllersService::generateProcessResponse(true,  'CREATE_SUCCESS', 200 , $vendor->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendor = Vendor::with('governorate' , 'region', 'user' , 'attachments.document')->find($id);
        return parent::success($vendor , 'تمت العملية بنجاح');
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
            'phone' => 'required|numeric|unique:vendors,phone,' . $id,
            'commercial_name' => 'nullable|string|max:255',
            'governorate_id' => 'nullable|exists:locations,id',
            'region_ids' => 'nullable|array|exists:locations,id',
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
        if ($validator->fails()) {
            return ControllersService::generateValidationErrorMessage($validator->errors()->first(), 200);
        }

        $vendor = Vendor::find($id);
        $vendor->name = $request->name;
        $vendor->commercial_name = $request->commercial_name;
        $vendor->phone = $request->phone;
        $vendor->active = $request->active;
        $vendor->governorate_id = $request->governorate_id;
        $vendor->region_id  = $request->region_id;
        $vendor->update();
        $user = User::find($vendor->user_id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        if($request->status){
            $user->status = $request->status;
        }
        $user->save();
        $OldVendorRegions = VendorRegions::where('vendor_id' , $vendor->id)->whereNotIn('region_id' , $request->region_ids)->delete();
        foreach ($request->region_ids as $value){
            $vendorRegions = VendorRegions::where('vendor_id' , $vendor->id)->where('region_id' , $value)->first();
            if(!$vendorRegions){
                VendorRegions::create([
                    'vendor_id' => $vendor->id,
                    'region_id' => $value,
                ]);
            }
        }
        $user = User::with('vendor.regions.region' , 'vendor.governorate')->find($user->id);
        return parent::success($user , "تم العملية بنجاح");
        return ControllersService::generateProcessResponse(true,  'UPDATE_SUCCESS', 200 , $vendor->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Vendor::find($id)->delete();
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }

    public function status(Request $request , $id)
    {
        $validator = Validator($request->all(), [
            'status' => 'required|in:ACTIVE,INACTIVE,WAITING,BLOCK',
        ], [
            'status.required' => 'يرجى أرسال الحالة',
            'status.in' => 'يرجى أختبار حالة بشكل صيحيح',
        ]);
        if (!$validator->fails()){
            $vendor = User::with('vendor')->find(Vendor::find($id)->user_id);
            $vendor->update(['status' => $request->status]);
            return parent::success($vendor , "تم العملية بنجاح");
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }

    public function active(Request $request , $id)
    {
        $validator = Validator($request->all(), [
            'active' => 'required|in:ACTIVE,INACTIVE',
        ], [
            'active.required' => 'يرجى أرسال الحالة',
            'active.in' => 'يرجى أختبار حالة بشكل صيحيح',
        ]);
        if (!$validator->fails()){
            $vendor = Vendor::with('user')->find($id);
            $vendor->update(['active' => $request->active]);
            return parent::success($vendor , "تم العملية بنجاح");
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }

}
