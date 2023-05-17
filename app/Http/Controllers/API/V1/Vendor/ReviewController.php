<?php

namespace App\Http\Controllers\API\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\ReviewCollection;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function __invoke(Request $request)
    {
        $review = Review::where('vendor_id' , Auth::user()->vendor->id)->with('customer' , 'order')->get();
        return (new ReviewCollection($review))->additional(['code' => 200 , 'status' => true , 'message' => 'تمت العملية بنجاح']);
    }
}

