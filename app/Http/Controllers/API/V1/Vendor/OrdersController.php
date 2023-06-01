<?php

namespace App\Http\Controllers\API\V1\Vendor;

use App\Events\UpdatedStatusOrder;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Models\Order;
use App\Models\OrderStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $order = Order::with('items', 'customer', 'address', 'statuses')
        ->filter([
            'status' => $request->status,
            'map' => $request->map,
            'type' => $request->type,
            'vendor_id' =>  Auth::user()->vendor->id,
        ])->select('id','type','customer_id','vendor_id','number','status','note','total','start_time','end_time','time','created_at')->latest()->get();

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'تمت العملية بنجاح',
            'count' => $order->count(),
            'data' => $order
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
        $order = Order::with('items', 'customer', 'address', 'statuses')
        ->where('vendor_id', Auth::user()->vendor->id)->where('id', $id)
            ->select(
                'id',
                'customer_id',
                'vendor_id',
                'number',
                'status',
                'note',
                'total',
                'start_time',
                'end_time',
                'time',
                'created_at'
            )
            ->latest()->get();
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
        $validator = Validator($request->all(), [
            'status' => 'nullable|in:ACCEPTED,DECLINED,DELIVERING,ONWAY,RECEIVED,PROCESSING,FILLED,DELIVERED,COMPLETED,CANCELLED_BY_VENDOR,CANCELLED_BY_CUSTOMER',
        ], [
            'status.in' => 'يرجى التأكد من الحالة الرسالة',
        ]);
        try {
            if (!$validator->fails()) {
                $order = Order::find($id);
                $order->updateStatus($request->status);
                // event(new UpdatedStatusOrder($order));
                return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
            }
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
        } catch (Exception $ex) { // Anything that went wrong
            return response([
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'reason_id' => 'nullable|exists:reasons,id',
            'status' => 'required|in:DECLINED,CANCELLED_BY_VENDOR',
        ], [
            'reason_id.exists' => 'لا يوجد سبب بهذا الكلام',
        ]);
        if (!$validator->fails()) {
            $order = Order::find($id);
            $order->update([
                'status' => $request->status,
            ]);
            $data = [
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'vendor_id' => Auth::user()->vendor->id,
                'status' => $request->status,
            ];
            if ($request->note) {
                $data['note'] = $request->note;
            }
            if ($request->reason_id) {
                $data['reason_id'] = $request->reason_id;
            }
            OrderStatus::create($data);
            return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }
}
