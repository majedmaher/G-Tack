<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
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
            'type' => 'required|in:ALL,GAS,WATER',
            'name' => 'required|max:255',
            'is_required' => 'required|in:1,0',
            'file' => 'required|in:IMAGE,FILE',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'validity' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'يرجى إدخال نوع الملف',
            'type.in' => 'لا يوجد نوع بهذا الاسم',
            'name.required' => 'يرجى أدخال أسم الملف',
            'name.max' => 'لا يمكن للأسم ان يكون أكبر من 255 حرف',
            'is_required.required' => 'يرجى أدخال ازا كان الملف أجبار ام لا',
            'is_required.in' => 'لا يمكن ارسال أجبار بهذا الأسم',
            'file.required' => 'يرجى أرسال نوع الملف المرفع',
            'file.in' => 'لا يوجد نوع ملف بهذا الأسم',
            'status.required' => 'يرجى أدخال الحالة الملف',
            'status.in' => 'لا توجد حالة بهذا الأسم',
            'validity.required' => '',
            'validity.numeric' => '',
        ];
    }
}
