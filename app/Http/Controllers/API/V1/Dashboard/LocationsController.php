<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\LocationStoreRequest;
use App\Http\Resources\GovernorateCollection;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Throwable;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locations = Location::when($request->parent , function ($q) use($request) {
            $q->where('parent_id' , $request->parent);
        })->when($request->type , function ($q) use($request) {
            $q->where('type' , $request->type);
        })->get();
        return (new GovernorateCollection($locations))->additional(['message' => 'تمت العملية بنجاح' , 'code' => 200 , 'status' => true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationStoreRequest $locationStoreRequest)
    {
        try {
            $location = Location::create($locationStoreRequest->all());
            return parent::success($location , "تم العملية بنجاح");
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = Location::find($id);
        return parent::success($location, 'تمت العملية بنجاح');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LocationStoreRequest $locationStoreRequest, $id)
    {
        try {
            $location = Location::find($id);
            $location->update($locationStoreRequest->all());
            return parent::success($location , "تم العملية بنجاح");
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Location::find($id)->delete();
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
