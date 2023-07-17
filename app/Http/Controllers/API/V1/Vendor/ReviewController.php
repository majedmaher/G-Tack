<?php

namespace App\Http\Controllers\API\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\ReviewCollection;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function index(Request $request)
    {
        $review = Review::where('vendor_id' , Auth::user()->vendor->id)
        ->when($request->type , function($q) use($request){
            $q->where('type' , $request->type);
        })
        ->when($request->order_id , function($q) use($request){
            $order_id = $request->order_id;
            $q->whereHas('order' , function($q) use ($order_id){
                $q->where('id' , 'LIKE' , '%'.$order_id.'%');
            });
        })
        ->with('customer' , 'order')->orderBy('rate', 'desc')->get();
        return (new ReviewCollection($review))->additional(['code' => 200 , 'status' => true , 'message' => 'تمت العملية بنجاح']);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator($data, [
            'rate' => 'required',
            'type' => 'required|in:CUSTOMER,VENDOR',
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'required|exists:orders,id',
            'feedback' => 'nullable|max:255',
        ], [
            'rate.required' => 'يرجى أرسال تقيم الخاص بك',
            'feedback.max' => 'يجب أن يكون الفيد باك 255 حرف  فقط',
            'customer_id.required' => 'يرجى أدخال الزبون',
            'customer_id.exists' => 'لا يوجد الزبون بهذا الأسم',
            'order_id.required' => 'يرجى أدخال رقم الطلب',
            'order_id.exists' => 'لا يوجد طلب بهذا الأسم',
            'type.required' => 'يرجى إرسال من هوه المقيم',
            'type.in' => 'يجب تحديد من النوع من خلال اختيار CUSTOMER,VENDOR',
        ]);
        if (!$validator->fails()) {
            $data['vendor_id'] = Auth::user()->vendor->id;
            $isSaved = Review::create($data);
            if ($isSaved) {
                return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
            } else {
                return ControllersService::generateProcessResponse(false, 'CREATE_FAILED', 400);
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
        }
    }

    public function rateVendor()
    {
        $rateWater = Review::where('vendor_id' , Auth::user()->vendor->id)->where('type' , 'CUSTOMER')
        ->whereHas('vendor' , function($q){ $q->where('type' , 'WATER');})->get();

        $rateGas = Review::where('vendor_id' , Auth::user()->vendor->id)->where('type' , 'CUSTOMER')
        ->whereHas('vendor' , function($q){ $q->where('type' , 'GAS');})->get();

        $data = [
            'rateSumWater' => $rateWater->sum('rate'),
            'rateCountWater' => $rateWater->count(),
            'rateSumGas' => $rateGas->sum('rate'),
            'rateCountGas' => $rateGas->count(),
        ];

        return parent::success($data , 'تمت العملية بنجاح');
    }
}

