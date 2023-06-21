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
    public function __invoke(Request $request)
    {
        return (match ($request->tap) {
            'vendors' => function () use ($request) {
                $vendorLocationInaccepted = Location::where('type', 'GOVERNORATE')->withCount('vendor_inaccepted')->get();
                $vendorsCountInaccepted = Vendor::when($request->type, function ($query) use ($request) {
                    $query->where('type', $request->type);
                })->whereHas('user', function ($q) {
                    $q->where('status', '!=', 'ACTIVE');
                })->count();
                $vendorLocationAccepted = Location::where('type', 'GOVERNORATE')->withCount('vendor_accepted')->get();
                $vendorsCountAccepted = Vendor::when($request->type, function ($query) use ($request) {
                    $query->where('type', $request->type);
                })->whereHas('user', function ($q) {
                    $q->where('status', 'ACTIVE');
                })->count();
                return parent::success([
                    'vendorLocationInaccepted' => $vendorLocationInaccepted, 'vendorsCountInaccepted' => $vendorsCountInaccepted,
                    'vendorLocationAccepted' => $vendorLocationAccepted, 'vendorsCountAccepted' => $vendorsCountAccepted
                ], 'تمت العملية بنجاح');
            },
            'customers' => function () {
                $customersLocation = Location::where('type', 'GOVERNORATE')->withCount('customer')->get();
                $customersCount = Customer::count();
                return parent::success([
                    'customersLocation' => $customersLocation,
                    'customersCount' => $customersCount
                ], 'تمت العملية بنجاح');
            },
            'salesTop' => function () use ($request) {
                $ordersCount = Order::count();
                $ordersGovernorate = Location::where('type', 'GOVERNORATE')->withCount('orders')->get();
                $ordersProduct = OrderItem::select('product_name', DB::raw('SUM(price) as sumOrders'),  DB::raw('COUNT(1) as countOrders'))
                    ->groupBy('product_id', 'product_name')->get();
                return parent::success([
                    'ordersGovernorate' => $ordersGovernorate, 'ordersCount' => $ordersCount,
                    'ordersProduct' => $ordersProduct, 'sumProductsPrice' => $ordersProduct->sum('sumOrders')
                ], 'تمت العملية بنجاح');
            },
            'sales' => function () use ($request) {
                $ordersGovernorate = Location::filter([
                    'from' => $request->from, 'to' => $request->to,
                    'postingTime' => $request->postingTime, 'type' => 'GOVERNORATE'
                ])->withCount('orders')->get();
                $ordersCount = Order::filter([
                    'from' => $request->from, 'to' => $request->to,
                    'postingTime' => $request->postingTime
                ])->count();
                $ordersRegion = Location::filter([
                    'from' => $request->from, 'to' => $request->to,
                    'postingTime' => $request->postingTime, 'type' => 'REGION'
                ])->withCount('orders2')->get();
                $ordersProduct = OrderItem::filter([
                    'from' => $request->from, 'to' => $request->to,
                    'postingTime' => $request->postingTime
                ])->select('product_name', DB::raw('SUM(price) as sumOrders'),  DB::raw('COUNT(1) as countOrders'))
                    ->groupBy('product_id', 'product_name')->get();
                $ordersVendor = Order::filterreport([
                    'from' => $request->from, 'to' => $request->to,
                    'postingTime' => $request->postingTime
                ])->select('name', DB::raw('COUNT(*) as count'))
                    ->join('vendors', 'vendors.id', 'orders.vendor_id')
                    ->groupBy(['vendors.name', 'vendors.id'])->limit(5)->get();
                $ordersCustomer = Order::filterreport([
                    'from' => $request->from, 'to' => $request->to,
                    'postingTime' => $request->postingTime
                ])->select('name', DB::raw('COUNT(*) as count'))
                    ->join('customers', 'customers.id', 'orders.customer_id')
                    ->groupBy(['customers.name', 'customers.id'])->limit(5)->get();
                return parent::success([
                    'ordersGovernorate' => $ordersGovernorate, 'ordersCount' => $ordersCount,
                    'ordersRegion' => $ordersRegion, 'ordersProduct' => $ordersProduct, 'ordersVendor' => $ordersVendor,
                    'ordersCustomer' => $ordersCustomer
                ], 'تمت العملية بنجاح');
            },
            default => function () {
                return parent::success(null, 'تأكد من التاب المرسل');
            }
        })();
    }
}
