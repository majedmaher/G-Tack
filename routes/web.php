<?php

use App\Models\Document;
use App\Models\User;
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
     $documentIds  = [];
     if (!is_array($documentIds)) {
         $documentIds = [];
     }
     $role = [];
     $document = Document::where('status', 'ACTIVE')->whereIn('type', ['ALL' , 'GAS'])
     ->when($documentIds  , function($q) use ($documentIds ){
         $q->whereIn('id' , $documentIds );
     })->get();
     $role['name'] = 'required|string|max:255';
     // $role['phone'] = 'required|numeric|unique:users,phone,' . $this->route('vendor');
     $role['type'] = 'required|in:CUSTOMER,VENDOR';
     $role['vendor_type'] = 'required|in:GAS,WATER';
     $role['commercial_name'] = 'required|string|max:255';
     $role['governorate_id'] = 'required|exists:locations,id';
     $role['region_ids'] = 'required|array|exists:locations,id';
     $role[$data_prefix . 'document_id'] = 'required|exists:documents,id';
     $role[$data_prefix . 'file'] = 'required|in:IMAGE,FILE';
     foreach ($document as $key => $value) {
         $is_required = $value->is_required == 1 ? "required" : "nullable";
         // $file = $value->file == "IMAGE" ? "image" : "file";
         $role['data.' . $key . '.' . $value->slug] = $is_required;
     }
     return $role;
});
