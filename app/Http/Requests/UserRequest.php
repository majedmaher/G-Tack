<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|max:255',
            'job_title' => 'required|max:255',
            'phone' => 'required|numeric|unique:users',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يرجى أدخال أسم المستخدم',
            'name.max' => 'لا يمكن لأسم المستخدم ان ابكون اكتر 255 حرف',
            'phone.required' => 'يرجى أدخال الهاتف',
            'phone.numeric' => 'لا يمكن للهاتف ان يكون نص',
            'phone.unique' => 'هذا الرقم موجود من قبل',
            'email.required' => 'يرجى أدخال ايميل',
            'email.email' =>  'يرجى أدخال ايميل',
            'email.unique' => 'هذه الايميل موجود مسبقا',
            'role_id.required' => 'يرجى أدخال المسمى الوظيفي',
            'role_id.exists' => 'لا يوجد مسمى بهذا الاسم',
        ];
    }
}
