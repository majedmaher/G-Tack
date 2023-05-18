<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewCollection;
use App\Models\Review;
use Illuminate\Http\Request;

class CustomerReviewsController extends Controller
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
        $reviewsCustomer = Review::where('customer_id' , $id)
        ->when($start, function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        })
        ->when($request->type , function($q) use($request){
            $q->where('type' , $request->type);
        })
        ->with('vendor' , 'customer' , 'order')
        ->latest()->paginate($countRow ?? 15);

        $data = [
            'reviews_count' => $reviewsCustomer->count(),
            'reviews_sum_total' => $reviewsCustomer->sum('rate'),
            'reviews' => new ReviewCollection($reviewsCustomer),
            'pages' => [
                'current_page' => $reviewsCustomer->currentPage(),
                'total' => $reviewsCustomer->total(),
                'page_size' => $reviewsCustomer->perPage(),
                'next_page' => $reviewsCustomer->nextPageUrl(),
                'last_page' => $reviewsCustomer->lastPage(),
            ]
        ];

        return parent::success($data , 'تمت العملية بنجاح');
    }
}
