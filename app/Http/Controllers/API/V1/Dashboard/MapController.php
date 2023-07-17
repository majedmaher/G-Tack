<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;

class MapController extends Controller
{

    public function __invoke(Request $request)
    {
        return (match ($request->tap) {
            'vendors' => function () use ($request) {
                $vendors = Vendor::whereHas('user', function ($qu) {
                    $qu->where('status', 'ACTIVE');
                })->with('governorate', 'regions', 'user', 'attachments.document')
                    ->withCount('reviews')
                    ->withSum('reviews', 'rate')
                    ->withSum('orders', 'time')
                    ->withCount('orders')
                    ->withAvg('orders', 'time')->get();
                return parent::success($vendors, "تمت العملية بنجاح");
            },
            'orders' => function () use ($request) {
                $orders = Order::with('items', 'vendor', 'customer', 'address', 'statuses')
                    ->where('status', '!=', 'COMPLETED')->get();
                return parent::success($orders, "تمت العملية بنجاح");
            },
            default => function () {
                return parent::success(null, 'تأكد من التاب المرسل');
            }
        })();
    }
}