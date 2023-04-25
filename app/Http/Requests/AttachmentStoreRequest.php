<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class AttachmentStoreRequest extends FormRequest
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
        $document = Document::where('status' , 'ACTIVE')->get();
        $role = [];
        foreach($document as $key => $value){
            $is_required = $value->is_required == 1 ? "required" : "nullable";
            $type = $value->type == "IMAGE" ? "image|mimes:jpeg,png|max:5000" : "file|mimes:pdf|max:5000";
            $role[$value->name] = $is_required . '|' . $type;
        }
        $role['document_id'] = 'required|exists:documents,id';
        return $role;
    }

    public function messages()
    {
        $document = Document::where('status' , 'ACTIVE')->get();
        $messages = [];
        foreach($document as $key => $value){
            $is_required = $value->is_required == 1 ? "required" : "nullable";
            $type = $value->type == "IMAGE" ? "image|mimes:jpeg,png|max:5000" : "file|mimes:pdf|max:5000";
            $messages[$value->name . '.' . $is_required] = $value->name .' يجب عليك أدخال الحقل';
            if($value->type == "IMAGE"){
                $messages[$value->name . '.image'] = "يجب عليك رفع صورة";
                $messages[$value->name . '.mimes'] = "إمتداد الصور المسموح بها هو jpeg , png";
                $messages[$value->name . '.max'] = "حجم الصوره المسموح به هو 5 بكسل";
            }else{
                $messages[$value->name . '.file'] = 'يجب عليك رفع ملف';
                $messages[$value->name . '.mimes'] = 'إمتداد الملف المسموح به هو pdf';
                $messages[$value->name . '.max'] = 'حجم الملف المسموح به هو 5 بكسل';
            }
        }
        $messages['document_id.required'] = "يجب عليك ان ترسل اسم الملف المرسل";
        $messages['document_id.exists'] = "لا يوجد ملفات تريدها بهذا الاسم";
        return $messages;
    }
}
