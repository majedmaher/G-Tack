<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Events\OrderCreated;
use App\Events\UpdatedStatusOrder;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Services\CreateOrderService;
use App\Services\ReOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $order = Order::with('items' , 'vendor' , 'address' , 'statuses' , 'reviews')
        ->filter([
            'status' => $request->status,
            'type' => $request->type,
            'map' => $request->map,
            'customer_id' =>  Auth::user()->customer->id,
        ])
        ->select('id' , 'vendor_id' , 'type' , 'number' , 'status' ,
         'note', 'total' , 'start_time' , 'end_time' , 'time', 'created_at')->latest()->get();

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
     * @param  \App\Http\Requests\CreateOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrderRequest $request, CreateOrderService $createOrderService)
    {
        $data = $request->all();
        // try {
            $order = $createOrderService->handle($data);
        // } catch (Throwable $e) {
        //     return response([
        //         'message' => $e->getMessage(),
        //     ], 500);
        // }
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with('items' , 'vendor' , 'address' , 'statuses')
        ->where('customer_id' , Auth::user()->customer->id)->where('id' , $id)
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
        $order = Order::find($id);
        $items = OrderItem::where('order_id' , $order->id)->whereIn('id' , [$request->list])->get();
        $order->items()->update([
            'product_name' => '123',
            'price' => '123',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request , $id)
    {
        $validator = Validator($request->all(), [
            'reason_id' => 'nullable|exists:reasons,id',
        ], [
            'reason_id.exists' => 'لا يوجد سبب بهذا الكلام',
        ]);
        if(!$validator->fails()){
            $order = Order::find($id);
            $order->update([
                'status' => 'CANCELLED_BY_CUSTOMER',
            ]);
            $data = [
                'order_id' => $order->id,
                'customer_id' => Auth::user()->customer->id,
                'vendor_id' => $order->vendor_id,
                'status' => 'CANCELLED_BY_CUSTOMER',
            ];
            if($request->note){
                $data ['note'] = $request->note;
            }
            if($request->reason_id){
                $data ['reason_id'] = $request->reason_id;
            }
            OrderStatus::create($data);
            event(new UpdatedStatusOrder($order));
            return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }

    public function reorder(Request $request, ReOrderService $reOrderService , $id)
    {
        try {
            $reOrderService->handle($id);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
        return ControllersService::generateProcessResponse(true, 'REORDER_SUCCESS', 200);
    }

}
