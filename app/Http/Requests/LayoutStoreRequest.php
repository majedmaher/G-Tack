<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LayoutStoreRequest extends FormRequest
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
            "title" => "required|max:255",
            "description" => "required|max:255",
            'image' => $this->getMethod() === 'POST' ? 'required' : 'nullable'.'|image',
            "type" => "required|in:CUSTOMER,VENDOR"
        ];
    }

    public function layoutData()
    {
        $data = $this->validated();
        if (isset($data['image'])) {
            $name = Str::random(12);
            $path = $data['image'];
            $name = $name . time() . '.' . $data['image']->getClientOriginalExtension();
            $path->move('uploads/layouts/', $name);
            $data['image'] = 'uploads/layouts/' . $name;
        }
        return $data;
    }
}
