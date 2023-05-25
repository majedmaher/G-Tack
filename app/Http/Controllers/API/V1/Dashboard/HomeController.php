<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $vendorsCount = Vendor::count();
        $customersCount = Customer::count();
        $vendorLocation = Location::withCount('vendor')->get();
        $ordersCount = Order::count();
        $ordersLocation = Location::withCount('orders')->get();
        $orders = Order::latest()->take(8)->get();
        $vendors = Vendor::latest()->take(8)->get();
        $data = [
            'ordersLocation' => $ordersLocation,
            'ordersCount' => $ordersCount,
            'vendorLocation' => $vendorLocation,
            'vendorsCount' => $vendorsCount,
            'users' => $vendorsCount  + $customersCount,
            'orders' => $orders,
            'vendors' => $vendors,
        ];
        return parent::success($data, 'تمت العملية بنجاح');
    }
}
