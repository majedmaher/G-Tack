<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewCollection;
use App\Models\Review;
use Illuminate\Http\Request;

class VendorReviewsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request , $id)
    {
        $countRow = $request->countRow;
        $start = $request->start;
        $end = $request->end;

        $reviewsVendor = Review::where('vendor_id' , $id)
        ->when($start, function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        })
        ->when($request->type , function($q) use($request){
            $q->where('type' , $request->type);
        })
        ->with('vendor' , 'customer' , 'order')
        ->latest()->paginate($countRow ?? 15);

        $data = [
            'reviews_count' => $reviewsVendor->count(),
            'reviews_sum_total' => $reviewsVendor->sum('rate'),
            'reviews' => new ReviewCollection($reviewsVendor),
            'pages' => [
                'current_page' => $reviewsVendor->currentPage(),
                'total' => $reviewsVendor->total(),
                'page_size' => $reviewsVendor->perPage(),
                'next_page' => $reviewsVendor->nextPageUrl(),
                'last_page' => $reviewsVendor->lastPage(),
            ]
        ];

        return parent::success($data , 'تمت العملية بنجاح');
    }
}
