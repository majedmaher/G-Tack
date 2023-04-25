<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $addresses = Address::where('customer_id' , Auth::user()->id)->get();
        return (new AddressCollection($addresses))->additional(['message' => 'تمت العملية بنجاح']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator($data, [
            'lat' => 'required|max:255',
            'lng' => 'required|max:255',
            'label' => 'required|string|max:255',
            'map_address' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ], [
            'lat.required' => 'يرجى إرسال الطوال الخاص ب الخريطة',
            'lat.max' => 'يجب ان لا يزيد الطول عن 255 خانة',
            'lng.required' => 'يرجى إرسال عرض الخاص ب الخريطة',
            'lng.max' => 'يجب ان لا يزيد عرض عن 255 خانة',
            'label.required' => 'يرحى أدخال الوسم الخاص ب العنوان',
            'label.max' => 'يجب ان لا يزيد وسم عن 255 خانة',
            'map_address.required' => 'يرحى أدخال  عنوان الخريطة الخاص ب العنوان',
            'map_address.max' => 'يجب ان لا يزيد عنوان الخريطة عن 255 خانة',
            'description.required' => 'يرحى أدخال الوصف الخاص ب العنوان',
            'description.max' => 'يجب ان لا يزيد الوصف عن 255 خانة',
        ]);
        if (!$validator->fails()) {
            $data['customer_id'] = Auth::user()->id;
            $isSaved = Address::create($data);
            if ($isSaved) {
                return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
            }
            return ControllersService::generateProcessResponse(false, 'CREATE_FAILED', 400);

        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $addresses = Address::where('customer_id' , Auth::user()->id)->where('id' , $id)
        ->select('id' , 'customer_id' , 'lat' , 'lng' , 'map_address' , 'description')->first();
        return parent::success($addresses , 'تمت العملية بنجاح');
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
        $data = $request->all();
        $validator = Validator($data, [
            'lat' => 'required|max:255',
            'lng' => 'required|max:255',
            'label' => 'required|string|max:255',
            'map_address' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ], [
            'lat.required' => 'يرجى إرسال الطوال الخاص ب الخريطة',
            'lat.max' => 'يجب ان لا يزيد الطول عن 255 خانة',
            'lng.required' => 'يرجى إرسال عرض الخاص ب الخريطة',
            'lng.max' => 'يجب ان لا يزيد عرض عن 255 خانة',
            'label.required' => 'يرحى أدخال الوسم الخاص ب العنوان',
            'label.max' => 'يجب ان لا يزيد وسم عن 255 خانة',
            'map_address.required' => 'يرحى أدخال  عنوان الخريطة الخاص ب العنوان',
            'map_address.max' => 'يجب ان لا يزيد عنوان الخريطة عن 255 خانة',
            'description.required' => 'يرحى أدخال الوصف الخاص ب العنوان',
            'description.max' => 'يجب ان لا يزيد الوصف عن 255 خانة',
        ]);
        if (!$validator->fails()) {
            $data['customer_id'] = Auth::user()->id;
            $isSaved = Address::find($id)->update($data);
            if ($isSaved) {
                return ControllersService::generateProcessResponse(true, 'UPDATE_SUCCESS', 200);
            } else {
                return ControllersService::generateProcessResponse(false, 'UPDATE_FAILED', 400);
            }
        } else {
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
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
        Address::find($id)->delete();
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }
}
