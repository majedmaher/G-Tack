<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $countRow = $request->countRow;
        $order = Order::with('items', 'vendor', 'customer', 'address', 'statuses')->where('status' , '!=' , 'COMPLETED')
            ->filter([
                'status' => $request->status,
                'type' => $request->type,
                'map' => $request->map,
            ])
            ->when($start, function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            })
            ->latest()->paginate($countRow ?? 15);
        return response()->json([
            'message' => 'تمت العمليه بنجاح',
            'code' => 200,
            'status' => true,
            'count' => $order->total(),
            'data' => new OrderCollection($order),
            'pages' => [
                'current_page' => $order->currentPage(),
                'total' => $order->total(),
                'page_size' => $order->perPage(),
                'next_page' => $order->nextPageUrl(),
                'last_page' => $order->lastPage(),
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
        $order = Order::with('items', 'vendor', 'customer', 'address', 'statuses')
            ->select('id', 'vendor_id', 'customer_id', 'number', 'status', 'note', 'total', 'start_time', 'end_time', 'time', 'created_at')
            ->find($id);
        return parent::success($order, 'تمت العملية بنجاح');
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
        $data = $request->all();
        $validator = Validator($data, [
            'lat' => 'required|max:255',
            'lng' => 'required|max:255',
            'label' => 'required|string|max:255',
            'map_address' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|in:ACCEPTED,DECLINED,ONWAY,PROCESSING,FILLED,DELIVERED,COMPLETED,CANCELLED_BY_VENDOR,CANCELLED_BY_CUSTOMER',
        ], [
            'lat.required' => 'يرجى إرسال الطوال الخاص ب الخريطة',
            'lat.max' => 'يجب ان لا يزيد الطول عن 255 خانة',
            'lng.required' => 'يرجى إرسال عرض الخاص ب الخريطة',
            'lng.max' => 'يجب ان لا يزيد عرض عن 255 خانة',
            'label.required' => 'يرحى أدخال الوسم الخاص ب العنوان',
            'label.max' => 'يجب ان لا يزيد وسم عن 255 خانة',
            'map_address.required' => 'يرحى أدخال  عنوان الخريطة الخاص ب العنوان',
            'map_address.max' => 'يجب ان لا يزيد عنوان الخريطة عن 255 خانة',
            'description.required' => 'يرحى أدخال الوصف الخاص ب العنوان',
            'description.max' => 'يجب ان لا يزيد الوصف عن 255 خانة',
            'status.required' => 'يرجى إرسال الحالة ل تعديل الطلب',
            'status.in' => 'يرجى التأكد من الحالة الرسالة',
        ]);

        if (!$validator->fails()) {
            $orderAddress = OrderAddress::find($id);
            $orderAddress->update($data);
            Order::find($orderAddress->order_id)->update(['status' => $data['status']]);
            return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
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
