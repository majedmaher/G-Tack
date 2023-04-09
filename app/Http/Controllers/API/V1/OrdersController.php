<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\OrderCollection;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $order = Order::with('items' , 'vendor' , 'address')
        ->when($status , function ($q) use ($status) {
            $q->where('status' , $status);
        })
        ->where('customer_id' , Auth::user()->id)
        ->select('id' , 'vendor_id' , 'number' , 'status' , 'note'
        , 'total' , 'start_time' , 'end_time' , 'time'
        , 'created_at')
        ->latest()->get();
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
        $customer_id = Auth::user()->id;
        $validator = Validator($data, [
            'vendor_id' => 'required|exists:vendors,id',
            'total' => 'required|numeric|integer',
            'note' => 'nullable|string',
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')->where(function ($query) use ($customer_id) {
                    $query->where('customer_id', $customer_id);
                })
            ],
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:jars,id',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|integer',
        ], [
            'vendor_id.required' => 'يرجى أدخال الموزع',
            'vendor_id.exists' => 'لا يوجد موزع بهذا الأسم',
            'address_id.required' => 'يرجى أدخال العنوان الخاص بك',
            'address_id.exists' => 'لا يوجد عنوان بهذا الأسم',
            'total.required' => 'يرجى أدخال المجموع الخاص ب الطلب',
            'total.numeric' => 'يجب أن يكون المجموع رقم',
            'total.integer' => 'يجب أن يكون المجموع رقم',
            'items.required' => 'يرجى أدخال الأنابيب',
            'items.array' => 'حدث خطأ في إدخال الأنابيب',
            'items.*.id.required' => 'يرجى أدخال أرقام الأنابيب',
            'items.*.id.integer' => 'يحب أن يكون أرقام الأنابيب رقم وليس نص',
            'items.*.id.exists' => 'لا توجد أنابيب بهذا الأسم',
            'items.*.quantity.required' => 'يرجى أدخال الكمية الخاصة ب الأنبوبة',
            'items.*.quantity.integer' => 'يحب أن يكون الكمية الأنبوبة رقم وليس نص',
            'items.*.price.required' => 'يرجى أدخال السعر الخاصة ب الأنبوبة',
            'items.*.price.integer' => 'يحب أن يكون سعر الأنبوبة رقم وليس نص',
        ]);
        if (!$validator->fails()) {
            $lastId = Order::latest()->first()->id ?? 1;
            $newOrder = Order::create([
                'number' => date('Y').$lastId,
                'customer_id' => Auth::user()->id,
                'vendor_id' => $data['vendor_id'],
                'note' => $data['note'],
                'total' => $data['total'],
            ]);
            $addressOrder = Address::find($data['address_id']);
            OrderAddress::create([
                'order_id' => $newOrder->id,
                'address_id' => $addressOrder->id,
                'label' => $addressOrder->label,
                'lat' =>  $addressOrder->lat,
                'lng' =>  $addressOrder->lng,
                'map_address' =>  $addressOrder->map_address,
                'description' =>  $addressOrder->description,
            ]);
            foreach ($data['items'] as $value){
                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'jar_id' => $value['id'],
                    'quantity' => $value['quantity'],
                    'price' => $value['price'],
                ]);
            }
            OrderStatus::create([
                'order_id' => $newOrder->id,
                'customer_id' => Auth::user()->id,
                'vendor_id' => $newOrder->vendor_id,
                'status' => 'PENDING',
                'note' => "1",
            ]);
            return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
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
        $order = Order::with('items' , 'vendor' , 'address')
        ->where('customer_id' , Auth::user()->id)->where('id' , $id)
        ->select('id' , 'vendor_id' , 'number' , 'status' , 'note'
        , 'total' , 'start_time' , 'end_time' , 'time'
        , 'created_at')
        ->latest()->get();
        return parent::success($order , 'تمت العملية بنجاح');
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
        $order = Order::find($id);
        $order->update([
            'status' => 'CANAEL',
        ]);
        OrderStatus::create([
            'order_id' => $order->id,
            'customer_id' => Auth::user()->id,
            'vendor_id' => $order->vendor_id,
            'status' => 'CANAEL',
            'note' => "1",
        ]);
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
