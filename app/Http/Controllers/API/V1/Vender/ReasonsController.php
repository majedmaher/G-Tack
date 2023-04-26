<?php

namespace App\Http\Controllers\API\V1\Vender;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReasonCollection;
use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonsController extends Controller
{
    public function __invoke(Request $request)
    {
        $type = $request->type;
        $context = $request->context;
        $reasons = Reason::where('status' , 'ACTIVE')
        ->where('type' , 'VENDOR')
        ->when($context , function ($q) use($context){
            $q->where('context' , $context);
        })
        ->get();
        return (new ReasonCollection($reasons))->additional(['message' => 'تمت العملية بنجاح']);
    }
}
