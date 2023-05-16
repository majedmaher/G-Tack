<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use Illuminate\Http\Request;

class VendorReviewsController extends Controller
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
        $ordersVendor = Order::where('vendor_id' , $id)->with('reviews')
        ->latest()->paginate($countRow ?? 15);

        return $ordersVendor;

        $data = [
            'orders_count' => $ordersVendor->count(),
            'orders_sum_total' => $ordersVendor->sum('total'),
            'orders_sum_time' => $ordersVendor->sum('time'),
            'orders_avg_time' => $ordersVendor->avg('time'),
            'orders' => new OrderCollection($ordersVendor),
        ];

        return parent::success($data , 'تمت العملية بنجاح');
    }
}
