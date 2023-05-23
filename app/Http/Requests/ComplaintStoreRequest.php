<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ComplaintStoreRequest extends FormRequest
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
        return [
            'vendor_type' => 'required|in:GAS,WATER',
            'type' => 'required|in:CUSTMER,VENDOR',
            'customer_id' => 'nullable|exists:customers,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'order_id' => 'required|exists:orders,id',
            'content' => 'required',
            'image' => 'nullable|image',
        ];
    }

    public function messages()
    {
        return [
            'vendor_type.required' => 'يرجى إدخال نوع الشكوى',
            'vendor_type.in' => 'لا يوجد نوع بهذا الاسم',
            'type.required' => 'يرجى إدخال نوع الشكوى',
            'type.in' => 'لا يوجد نوع بهذا الاسم',
            'customer_id.exists' => 'لا يوجد رقم مستخدم بهذا الاسم',
            'vendor_id.exists' => 'لا يوجد رقم الموزع بهذا الاسم',
            'order_id.required' => 'يرجى أرسال رقم الطلب',
            'order_id.exists' => 'لا يوجد رقم طلب بهذا الاسم',
            'content.required' => 'يرجى ارسال سبب الشكوى',
            'image.image' => 'يرجى أرسال الصوره صورة',
        ];
    }

    public function complaintData()
    {
        $data = $this->validated();
        if (isset($data['image'])) {
            $name = Str::random(12);
            $path = $data['image'];
            $name = $name . time() . '.' . $data['image']->getClientOriginalExtension();
            $path->move('uploads/Complaints/', $name);
            $data['image'] = 'uploads/Complaints/' . $name;
        }
        return $data;
    }
}
