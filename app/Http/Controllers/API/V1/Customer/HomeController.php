<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $products = Product::when($request->type , function($q) use ($request){
            $q->where('type' , $request->type);
        })->get();
        return (new ProductCollection($products))->additional(['message' => 'تمت العملية بنجاح']);
    }

    public function settings(Request $request)
    {
        $setting = Setting::when($request->key , function($q) use ($request){
            $q->where('key' , $request->key);
        })->get();
        return parent::success($setting , 'تمت العملية بنجاح');
    }
}
