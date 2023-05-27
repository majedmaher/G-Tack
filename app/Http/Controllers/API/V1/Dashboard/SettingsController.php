<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $settings = Setting::when($request->key , function($q) use ($request){
            $q->where('key' , $request->key);
        })->where('group','social')->get();
        return parent::success($settings , "تم العملية بنجاح");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        foreach ($data as $k => $v) {
            $this->update_setting([
                'key' => $k,
                'value' => $v
            ], $k);
        }
        return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
    }

    public function update_setting($data,$key){
        Setting::where('key',$key)->update($data);
    }

}
