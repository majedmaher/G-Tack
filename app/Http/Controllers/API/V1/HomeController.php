<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JarCollection;
use App\Models\Jar;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $jars = Jar::get();
        return (new JarCollection($jars))->additional(['message' => 'تمت العملية بنجاح']);
    }

    public function settings(Request $request)
    {
        $setting = Setting::where('key' , $request->key)->first();

        return parent::success($setting , 'تمت العملية بنجاح');
    }
}
