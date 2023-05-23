<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\ComplaintStoreRequest;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ComplaintStoreRequest $complaintStoreRequest)
    {
        Complaint::create($complaintStoreRequest->complaintData());
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
    }
}
