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
        $products = Product::get();
        return (new ProductCollection($products))->additional(['message' => 'تمت العملية بنجاح']);
    }

    public function settings(Request $request)
    {
        $setting = Setting::where('key' , $request->key)->first();
        return parent::success($setting , 'تمت العملية بنجاح');
    }
}
