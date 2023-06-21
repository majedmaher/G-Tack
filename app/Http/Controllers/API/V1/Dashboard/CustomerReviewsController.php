<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewCollection;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerReviewsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request , $id = null)
    {
        $countRow = $request->countRow;
        $reviewsCustomer = Review::
        when($request->postingTime, function ($builder) use ($request) {
            $value = $request->postingTime;
            $weekAgo = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
            $monthAgo = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $yearAgo = Carbon::now()->startOfYear()->format('Y-m-d H:i:s');
            $last24Hours = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            if ($value == '24') {
                $builder->whereBetween('created_at', [$last24Hours, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'week') {
                $builder->whereBetween('created_at', [$weekAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'month') {
                $builder->whereBetween('created_at', [$monthAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'year') {
                $builder->whereBetween('created_at', [$yearAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            }
        })
        ->when($id , function($q) use($id){
            $q->where('customer_id' , $id);
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
