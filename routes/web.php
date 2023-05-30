<?php

use App\Models\Document;
use App\Models\Order;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('test' , function(){
    $data_prefix = 'data.*.';
    $document = Document::where('status' , 'ACTIVE')->whereIn('type' , ['ALL'])->get();
    $messages = [];
    foreach($document as $key => $value){
        $is_required = $value->is_required == 1 ? "required" : "nullable";
        $file = $value->file == "IMAGE" ? "image|mimes:jpeg,png|max:5000" : "file|mimes:pdf|max:5000";
        $messages[$value->name . '.' . $is_required] = $value->name .' يجب عليك أدخال الحقل';
        if($value->file == "IMAGE"){
            $messages[$value->name . '.image'] = "يجب عليك رفع صورة";
            $messages[$value->name . '.mimes'] = "إمتداد الصور المسموح بها هو jpeg , png";
            $messages[$value->name . '.max'] = "حجم الصوره المسموح به هو 5 بكسل";
        }else{
            $messages[$value->name . '.file'] = 'يجب عليك رفع ملف';
            $messages[$value->name . '.mimes'] = 'إمتداد الملف المسموح به هو pdf';
            $messages[$value->name . '.max'] = 'حجم الملف المسموح به هو 5 بكسل';
        }
    }
    $messages[$data_prefix.'document_id.required'] = "يجب عليك ان ترسل اسم الملف المرسل";
    $messages[$data_prefix.'document_id.exists'] = "لا يوجد ملفات تريدها بهذا الاسم";
    $messages[$data_prefix.'file.required'] = "يجب عليك ان ترسل نوع الملف المرسل";
    $messages[$data_prefix.'file.in'] = "لا يوجد نوع بهذا الأسم";
    return $messages;
});
