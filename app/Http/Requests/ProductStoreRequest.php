<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ProductStoreRequest extends FormRequest
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
            'type' => 'required|in:GAS,WATER',
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'size' => 'required|numeric',
            'image' => 'required|image',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'يرجى أرسال نوع المنتج',
            'type.in' => 'لا يوجد نوع بهذا الأسم',
            'name.required' => 'يرجى أرسال اسم المنتج',
            'name.max' => 'لا يمكن للأسم ان يكون أكثر من 255 حرف',
            'price.required' => 'يرجى أرسال سعر المنتج',
            'price.numeric' => 'يجب أن يكون السعر رقم',
            'size.required' => 'يرجى أرسال سعت المنتج',
            'size.numeric' => 'يجب أن يكون سعت رقم',
            'image.required' => 'يرجى أرسال صورة المنتج',
            'image.image' => 'يجب أن يكون الصورة صورة',
        ];
    }

    public function userData()
    {
        $data = $this->validated();
        if (isset($data['image'])) {
            $name = Str::random(12);
            $path = $data['image'];
            $name = $name . time() . '.' . $data['image']->getClientOriginalExtension();
            $path->move('uploads/Products/', $name);
            $data['image'] = 'uploads/Products/' . $name;
        }
        return $data;
    }
}
