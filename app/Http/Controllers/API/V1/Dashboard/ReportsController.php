<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $ordersCount = Order::when($request->start, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->start, $request->end]);
        })->count();

        $ordersGovernorate = Location::when($request->start, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->start, $request->end]);
        })->withCount('orders')->get();

        $ordersRegion = Location::when($request->start, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->start, $request->end]);
        })->withCount('orders2')->get();

        $ordersProduct = OrderItem::when($request->start, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->start, $request->end]);
        })->select('product_name' , DB::raw('SUM(price) as sumOrders'))
        ->groupBy('product_name')->get();

        $ordersProductCount = OrderItem::when($request->start, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->start, $request->end]);
        })->select('product_name' , DB::raw('COUNT(*) as countOrders'))
        ->groupBy('product_name')->get();

        $ordersVendor = Order::when($request->start, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->start, $request->end]);
        })->select('name' , DB::raw('COUNT(*) as count'))
        ->join('vendors', 'vendors.id' , 'orders.vendor_id')
        ->groupBy(['vendors.name' , 'vendors.id'])->
        limit(5)->get();

        $ordersCustomer = Order::when($request->start, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->start, $request->end]);
        })->select('name' , DB::raw('COUNT(*) as count'))
        ->join('customers', 'customers.id' , 'orders.customer_id')
        ->groupBy(['customers.name' , 'customers.id'])->
        limit(5)->get();

        $data = [
            'ordersGovernorate' => $ordersGovernorate,
            'ordersCount' => $ordersCount,
            'ordersProduct' => $ordersProduct,
            'ordersTotal' => Order::sum('total'),
            'ordersRegion' => $ordersRegion,
            'ordersCount' => $ordersCount,
            'ordersProductCount' => $ordersProductCount,
            'ordersVendor' => $ordersVendor,
            'ordersCustomer' => $ordersCustomer,
        ];
        return parent::success($data, 'تمت العملية بنجاح');
    }
}
