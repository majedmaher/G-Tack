<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorOrdersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request , $id)
    {
        $countRow = $request->countRow;
        $ordersVendor = Order::where('vendor_id' , $id)->
        filter([
            'status' => $request->status,
            'type' => $request->type,
            'from' => $request->from,
            'to' => $request->to,
            'postingTime' => $request->postingTime,
        ])->with('items', 'vendor', 'customer', 'address', 'statuses')
        ->latest()->paginate($countRow ?? 15);

        $data = [
            'orders_count' => $ordersVendor->count(),
            'orders_sum_total' => $ordersVendor->sum('total'),
            'orders_sum_time' => $ordersVendor->sum('time'),
            'orders_avg_time' => $ordersVendor->avg('time'),
            'orders' => new OrderCollection($ordersVendor),
            'pages' => [
                'current_page' => $ordersVendor->currentPage(),
                'total' => $ordersVendor->total(),
                'page_size' => $ordersVendor->perPage(),
                'next_page' => $ordersVendor->nextPageUrl(),
                'last_page' => $ordersVendor->lastPage(),
            ]
        ];

        return parent::success($data , 'تمت العملية بنجاح');
    }
}
