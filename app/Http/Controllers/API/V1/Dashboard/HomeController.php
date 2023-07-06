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

        $vendorLocation = Location::where('type' , 'GOVERNORATE')->withCount('vendor')->get();

        $vendorsCount = Vendor::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->count();

        $customersLocation = Location::where('type' , 'GOVERNORATE')->withCount('customer')->get();

        $customersCount = Customer::count();

        $ordersCount = Order::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->count();

        $ordersLocation = Location::where('type' , 'GOVERNORATE')->withCount('orders')->get();

        $orders = Order::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->with('items', 'vendor', 'customer', 'address', 'statuses')->latest()->take(8)->get();

        $vendors = Vendor::when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->whereHas('user' , function($qu) {
            $qu->where('status' , 'WAITING');
        })->latest()->take(8)->get();

        $data = [
            'ordersLocation' => $ordersLocation,
            'ordersCount' => $ordersCount,
            'vendorLocation' => $vendorLocation,
            'vendorsCount' => $vendorsCount,
            'customersLocation' => $customersLocation,
            'customersCount' => $customersCount,
            'users' => $vendorsCount  + $customersCount,
            'orders' => $orders,
            'vendors' => $vendors,
            'downloads' => 145,
        ];
        return parent::success($data, 'تمت العملية بنجاح');
    }

}
