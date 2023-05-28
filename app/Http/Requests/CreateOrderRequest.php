<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $customer_id = Auth::user()->customer->id;
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'governorate_id' => 'required|exists:locations,id',
            'region_id' => 'required|exists:locations,id',
            'total' => 'required|numeric|integer',
            'type' => 'required|in:GAS,WATER',
            'note' => 'nullable|string',
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')->where(function ($query) use ($customer_id) {
                    $query->where('customer_id', $customer_id);
                })
            ],
            'items' => 'required|array',
            'items.*.id' => 'nullable|integer|exists:products,id',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|integer',
            'items.*.custom' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'vendor_id.required' => 'يرجى أدخال الموزع',
            'vendor_id.exists' => 'لا يوجد موزع بهذا الأسم',
            'governorate_id.exists' => 'لا يوجد منطقة بهذا الأسم',
            'governorate_id.required' => 'يرجى أدخال منطقة',
            'region_id.exists' => 'لا يوجد الحي بهذا الأسم',
            'region_id.required' => 'يرجى أدخال الحي',
            'address_id.required' => 'يرجى أدخال العنوان الخاص بك',
            'address_id.exists' => 'لا يوجد عنوان بهذا الأسم',
            'total.required' => 'يرجى أدخال المجموع الخاص ب الطلب',
            'total.numeric' => 'يجب أن يكون المجموع رقم',
            'total.integer' => 'يجب أن يكون المجموع رقم',
            'items.required' => 'يرجى أدخال الأنابيب',
            'items.array' => 'حدث خطأ في إدخال الأنابيب',
            'items.*.id.required' => 'يرجى أدخال أرقام الأنابيب',
            'items.*.id.integer' => 'يحب أن يكون أرقام الأنابيب رقم وليس نص',
            'items.*.id.exists' => 'لا توجد أنابيب بهذا الأسم',
            'items.*.quantity.required' => 'يرجى أدخال الكمية الخاصة ب الأنبوبة',
            'items.*.quantity.integer' => 'يحب أن يكون الكمية الأنبوبة رقم وليس نص',
            'items.*.price.required' => 'يرجى أدخال السعر الخاصة ب الأنبوبة',
            'items.*.price.integer' => 'يحب أن يكون سعر الأنبوبة رقم وليس نص',
            'items.*.custom.integer' => 'يحب أن يكون الكستم الأنبوبة رقم وليس نص',
        ];
    }
}
