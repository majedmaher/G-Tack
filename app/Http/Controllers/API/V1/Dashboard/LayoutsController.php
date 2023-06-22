<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\LayoutStoreRequest;
use App\Http\Resources\LayoutCollection;
use App\Models\Layout;
use App\Services\CreatedLog;
use Illuminate\Http\Request;
use Throwable;

class LayoutsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $layouts = Layout::when($request->type , function ($q) use($request) {
            $q->where('type' , $request->type);
        })->get();
        return (new LayoutCollection($layouts))->additional(['message' => 'تمت العملية بنجاح']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LayoutStoreRequest $layoutStoreRequest)
    {
        try {
            $layout = Layout::create($layoutStoreRequest->layoutData());
            CreatedLog::handle('أضافة شاشة جديدة');
            return parent::success($layout , "تم العملية بنجاح");
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $layout = Layout::find($id);
        return parent::success($layout, 'تمت العملية بنجاح');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LayoutStoreRequest $layoutStoreRequest, $id)
    {
        try {
            $layout = Layout::find($id);
            $layout->update($layoutStoreRequest->layoutData());
            CreatedLog::handle('تعديل شاشة');
            return parent::success($layout , "تم العملية بنجاح");
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Layout::find($id)->delete();
        CreatedLog::handle('حذف شاشة');
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
