<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->status;
        $order = Order::with('items' , 'vendor')
        ->when($status , function ($q) use ($status) {
            $q->where('status' , $status);
        })
        ->where('customer_id' , Auth::user()->id)
        ->select('id' , 'vendor_id' , 'number' , 'status' , 'note'
        , 'total' , 'start_time' , 'end_time' , 'time'
        , 'created_at')
        ->get();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'تمت العملية بنجاح' ,
            'count' => $order->count(),
            'data' => $order
            ] , 200);
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
            'vendor_id' => 'required|exists:vendors,id',
            'total' => 'required|numeric|integer',
            'start_time' => 'required|date_format:H:i:s',
            'note' => 'nullable|string',
        ], [
            'address_id.required' => 'يرجى تحديد الموقع الخاص بك',
            'address_id.exists' => 'لا يوجد عنوان بهذا الأسم',
            'vendor_id.required' => 'يرجى أدخال الموزع',
            'vendor_id.exists' => 'لا يوجد موزع بهذا الأسم',
            'total.required' => 'يرجى أدخال المجموع الخاص ب الطلب',
            'total.numeric' => 'يجب أن يكون المجموع رقم',
            'total.integer' => 'يجب أن يكون المجموع رقم',
            'start_time.required' => 'يجب أدخال تاريخ بداية الطلب',
            'start_time.date_format' => 'يرجى التأكد من الوقت المدخل',
        ]);
        if (!$validator->fails()) {
            $data['customer_id'] = Auth::user()->id;
            $isSaved = Order::create($data);
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
        Order::find($id)->delete();
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
