<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\ReviewCollection;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $review = Review::where('customer_id' , Auth::user()->id)->with('vendor' , 'customer' , 'order')->get();
        return (new ReviewCollection($review))->additional(['message' => 'تمت العملية بنجاح']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator($data, [
            'rate' => 'required',
            'vendor_id' => 'required|exists:vendors,id',
            'order_id' => 'required|exists:orders,id',
            'feedback' => 'nullable|max:255',
        ], [
            'rate.required' => 'يرجى أرسال تقيم الخاص بك',
            'feedback.max' => 'يجب أن يكون الفيد باك 255 حرف  فقط',
            'vendor_id.required' => 'يرجى أدخال الموزع',
            'vendor_id.exists' => 'لا يوجد موزع بهذا الأسم',
            'order_id.required' => 'يرجى أدخال رقم الطلب',
            'order_id.exists' => 'لا يوجد طلب بهذا الأسم',
        ]);
        if (!$validator->fails()) {
            $data['customer_id'] = Auth::user()->id;
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
        //
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
}
