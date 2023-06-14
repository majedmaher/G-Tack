<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\ComplaintStoreRequest;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $countRow = $request->countRow;
        $complaints = Complaint::when($start, function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        })->when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->when($request->vendor_type, function ($query) use ($request) {
            $query->where('vendor_type', $request->vendor_type);
        })->latest()->paginate($countRow ?? 15);

        return response()->json([
            'message' => 'تمت العمليه بنجاح',
            'code' => 200,
            'status' => true,
            'count' => $complaints->total(),
            'data' => ComplaintResource::collection($complaints),
            'pages' => [
                'current_page' => $complaints->currentPage(),
                'total' => $complaints->total(),
                'page_size' => $complaints->perPage(),
                'next_page' => $complaints->nextPageUrl(),
                'last_page' => $complaints->lastPage(),
            ]
        ], 200);
        return parent::success($complaints, 'تمت العملية بنجاح');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ComplaintStoreRequest $complaintStoreRequest)
    {
        Complaint::create($complaintStoreRequest->complaintData());
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
    }
}
