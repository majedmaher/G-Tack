<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorCollection;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $show = $request->show;
        $countRow = $request->countRow;
        $vendors = Vendor::
        when($show == 'new', function($q) use($show){
            $q->whereHas('user' , function($qu) use($show) {
                $qu->where('status' , 'WAITING');
            });
        })
        ->when($show == 'old', function($q) use($show){
            $q->whereHas('user' , function($qu) use($show) {
                $qu->where('status' , 'ACTIVE');
            });
        })
        ->with('governorate' , 'region' , 'user')
        ->latest()->paginate($countRow ?? 15);

        return response()->json([
            'message' => 'تمت العمليه بنجاح',
            'code' => 200,
            'status' => true,
            'count' => $vendors->total(),
            'data' => new VendorCollection($vendors),
            'pages' => [
                'current_page' => $vendors->currentPage(),
                'total' => $vendors->total(),
                'page_size' => $vendors->perPage(),
                'next_page' => $vendors->nextPageUrl(),
                'last_page' => $vendors->lastPage(),
            ]
        ], 200);

        return (new VendorCollection($vendors))->additional(['message' => 'تمت العملية بنجاح']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendor = Vendor::
        with('governorate' , 'region', 'user' , 'attachments.document')->find($id);

        return parent::success($vendor , 'تمت العملية بنجاح');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
