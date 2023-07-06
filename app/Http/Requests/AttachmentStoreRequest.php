<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class AttachmentStoreRequest extends FormRequest
{

    private $data_prefix = 'data.*.';

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
        $documentIds  = $this->input('document_ids');
        if (!is_array($documentIds)) {
            $documentIds = [];
        }
        $document = Document::where('status', 'ACTIVE')->whereIn('type', ['ALL' , $this->user()->vendor->type])
        ->when($documentIds  , function($q) use ($documentIds ){
            $q->whereIn('id' , $documentIds );
        })->get();
        $role = [];
        foreach ($document as $key => $value) {
            $is_required = $value->is_required == 1 ? "required" : "nullable";
            $file = $value->file == "IMAGE" ? "image|mimes:jpeg,png|max:5000" : "file|mimes:pdf";
            $role[$value->slug] = $is_required . '|' . $file;
        }
        $role[$this->data_prefix . 'document_id'] = 'required|exists:documents,id';
        $role[$this->data_prefix . 'file'] = 'required|in:IMAGE,FILE';
        return $role;
    }

    public function messages()
    {
        $document = Document::where('status', 'ACTIVE')->whereIn('type', ['ALL' , $this->user()->vendor->type])->get();
        $messages = [];
        foreach ($document as $key => $value) {
            $is_required = $value->is_required == 1 ? "required" : "nullable";
            $file = $value->file == "IMAGE" ? "image|mimes:jpeg,png|max:5000" : "file|mimes:pdf|max:5000";
            $messages[$value->slug . '.' . $is_required] = $value->slug . ' يجب عليك أدخال الحقل';
            if ($value->file == "IMAGE") {
                $messages[$value->slug . '.image'] = "يجب عليك رفع صورة";
                $messages[$value->slug . '.mimes'] = "إمتداد الصور المسموح بها هو jpeg , png";
                $messages[$value->slug . '.max'] = "حجم الصوره المسموح به هو 5 بكسل";
            } else {
                $messages[$value->slug . '.file'] = 'يجب عليك رفع ملف';
                $messages[$value->slug . '.mimes'] = 'إمتداد الملف المسموح به هو pdf';
                $messages[$value->slug . '.max'] = 'حجم الملف المسموح به هو 5 بكسل';
            }
        }
        $messages[$this->data_prefix . 'document_id.required'] = "يجب عليك ان ترسل اسم الملف المرسل";
        $messages[$this->data_prefix . 'document_id.exists'] = "لا يوجد ملفات تريدها بهذا الاسم";
        $messages[$this->data_prefix . 'file.required'] = "يجب عليك ان ترسل نوع الملف المرسل";
        $messages[$this->data_prefix . 'file.in'] = "لا يوجد نوع بهذا الأسم";
        return $messages;
    }
}
