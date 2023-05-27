<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\LayoutStoreRequest;
use App\Http\Resources\LayoutCollection;
use App\Models\Layout;
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
            Layout::create($layoutStoreRequest->layoutData());
            return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
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
            Layout::find($id)->update($layoutStoreRequest->layoutData());
            return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
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
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
