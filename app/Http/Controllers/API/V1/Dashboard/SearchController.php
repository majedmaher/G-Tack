<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function __invoke(Request $request)
    {
        $vendors = Vendor::where('id', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('name', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('commercial_name', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('phone', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('max_orders', 'LIKE' , '%' . $request->search . '%')->get();

        $customers = Customer::where('id', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('name', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('phone', 'LIKE' , '%' . $request->search . '%')->get();

        $orders = Order::where('id', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('number', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('total', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('status', 'LIKE' , '%' . $request->search . '%')->get();

        $users = User::where('type' , 'USER')
        ->where('id', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('name', 'LIKE' , '%' . $request->search . '%')->
        Orwhere('phone', 'LIKE' , '%' . $request->search . '%')->get();

        $data = [];

        foreach($vendors as $key => $value){
            $data['vendors'][$key]['id'] = 'رقم الموزع ' . $value->id;
            $data['vendors'][$key]['name'] = 'الموزع ' . $value->name;
        }

        foreach($customers as $key => $value){
            $data['customers'][$key]['id'] = 'رقم الزبون ' . $value->id;
            $data['customers'][$key]['name'] = 'الزبون ' . $value->name;
        }

        foreach($orders as $key => $value){
            $data['orders'][$key]['id'] = 'رقم الطلب ' . $value->id;
            $data['orders'][$key]['name'] = 'طلب ' . $value->type;
        }

        foreach($users as $key => $value){
            $data['users'][$key]['id'] = 'رقم مستخدم ' . $value->id;
            $data['users'][$key]['name'] = 'أسم المستخدم ' . $value->name;
        }

        $combinedArray = array_merge($data['vendors'], $data['customers'], $data['orders'], $data['users']);

        return $combinedArray;
    }
}
