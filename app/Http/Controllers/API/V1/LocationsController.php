<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GovernorateCollection;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $parent = $request->parent;
        $locations = Location::when($parent , function ($q) use($parent) {
            $q->where('parent_id' , $parent);
        }, function ($q){
            $q->where('parent_id' , null);
        })->get();
        return (new GovernorateCollection($locations))->additional(['message' => 'تمت العملية بنجاح']);
    }
}
