<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Services\CreatedLog;
use Illuminate\Http\Request;
use Throwable;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::
        when($request->type, function ($query) use ($request) {
            $query->where('type', $request->type);
        })->latest()->get();
        return (new ProductCollection($products));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $productStoreRequest)
    {
        try {
            $product = Product::create($productStoreRequest->userData());
            CreatedLog::handle('أضافة منتج جديد');
            return parent::success($product , "تم العملية بنجاح");
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
        $product = Product::find($id);
        return parent::success($product, 'تمت العملية بنجاح');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductStoreRequest $productStoreRequest, $id)
    {
        try {
            $product = Product::find($id);
            $product->update($productStoreRequest->userData());
            CreatedLog::handle('تعديل منتج');
            return parent::success($product , "تم العملية بنجاح");
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
        Product::find($id)->delete();
        CreatedLog::handle('حذف منتج');
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }

    public function status(Request $request , $id)
    {
        $validator = Validator($request->all(), [
            'status' => 'required|in:ACTIVE,INACTIVE',
        ], [
            'status.required' => 'يرجى أرسال الحالة',
            'status.in' => 'يرجى أختبار حالة بشكل صيحيح',
        ]);
        if (!$validator->fails()){
            $product = Product::find($id);
            $product->update(['status' => $request->status]);
            return parent::success($product , "تم العملية بنجاح");
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }
}
