<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\VendorCollection;
use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $governorate_id = $request->governorate_id;
        $region_id = $request->region_id;

        $vendor = Vendor::where('active' , 'ACTIVE')
        ->whereHas('user' , function($q){
            $q->where('status' , 'ACTIVE');
        })
        ->when($name , function ($q) use($name){
            $q->where('name' , 'LIKE' , '%'.$name.'%');
        })
        ->when($governorate_id , function ($q) use($governorate_id){
            $q->where('governorate_id' , $governorate_id);
        })
        ->when($region_id , function ($q) use($region_id){
            $q->where('region_id' , $region_id);
        })
        ->whereHas('governorate' , function($q){
            $q->where('status' , 'ACTIVE');
        })
        ->whereHas('region' , function($q){
            $q->where('status' , 'ACTIVE');
        })
        ->with('governorate' , 'region' , 'user')
        ->withCount('reviews')
        ->withSum('reviews' , 'rate')
        ->withSum('orders' , 'time')
        ->withCount('orders')
        ->withAvg('orders' , 'time')
        ->get();
        return (new VendorCollection($vendor))->additional(['message' => 'تمت العملية بنجاح']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
