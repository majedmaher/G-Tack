<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
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
        $condition = $request->condition;
        $start = $request->start;
        $end = $request->end;
        $order = Order::with('vendor', 'customer')
            ->filter([
                'status' => $request->status,
                'created_at' => $request->created,
            ])
            ->when($condition , function($query) use ($start , $end){
                $query->whereBetween('created_at', [$start , $end]);
            })
            ->select('id', 'vendor_id', 'customer_id', 'number', 'status', 'note', 'total', 'start_time', 'end_time', 'time', 'created_at')->latest()->paginate();
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
            ->select(
                'id',
                'vendor_id',
                'customer_id',
                'number',
                'status',
                'note',
                'total',
                'start_time',
                'end_time',
                'time',
                'created_at'
            )
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
            'status' => 'CANCELLED_BY_CUSTOMER',
        ]);
        $data = [
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'vendor_id' => $order->vendor_id,
            'status' => 'CANCELLED_BY_CUSTOMER',
        ];
        OrderStatus::create($data);
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
