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
        $vendorsCount = Vendor::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->count();

        $customersCount = Customer::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->count();

        $vendorLocation = Location::withCount('vendor')->get();

        $ordersCount = Order::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->count();

        $ordersLocation = Location::withCount('orders')->get();

        $orders = Order::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->with('vendor' , 'customer')->latest()->take(8)->get();

        $vendors = Vendor::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->latest()->take(8)->get();

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
