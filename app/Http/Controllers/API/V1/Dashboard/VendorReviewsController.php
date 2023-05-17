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
        $reviewsVendor = Review::where('vendor_id' , $id)->with('customer' , 'order')
        ->latest()->paginate($countRow ?? 15);

        $data = [
            'orders_count' => $reviewsVendor->count(),
            'orders_sum_total' => $reviewsVendor->sum('total'),
            'orders_sum_time' => $reviewsVendor->sum('time'),
            'orders_avg_time' => $reviewsVendor->avg('time'),
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
