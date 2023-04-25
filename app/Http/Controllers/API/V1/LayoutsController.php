<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LayoutCollection;
use App\Models\Layout;
use Illuminate\Http\Request;

class LayoutsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $layouts = Layout::where('status' , 'ACTIVE')->get();
        return (new LayoutCollection($layouts))->additional(['message' => 'تمت العملية بنجاح']);
    }
}
