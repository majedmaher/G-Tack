<?php

namespace App\Http\Controllers\API\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\AttachmentStoreRequest;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $document = Document::when($request->type, function($q) use($request) {
            $q->whereIn('type' , [$request->type , 'ALL']);
        })->where('status' , 'ACTIVE')->get();
        return parent::success($document , "تم العملية بنجاح");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttachmentStoreRequest $attachmentStoreRequest)
    {
        $data = $attachmentStoreRequest->all();
                foreach($data['data'] as $value){
                    $document = Document::where('id' , $value['document_id'])->first();
                if ($attachmentStoreRequest->hasFile($document->name)) {
                    $file = $attachmentStoreRequest->file($document->name);
                    $fileName = time() . '_' . '.' . $file->getClientOriginalExtension();
                    if($value['file'] == "IMAGE"){
                        $file->move('image/vendors', $fileName);
                        $data['file_path'] = 'image/vendors/' . $fileName;
                    }else{
                        $file->move('file/vendors', $fileName);
                        $data['file_path'] = 'file/vendors/' . $fileName;
                    }
                }
                $data['document_id'] = $value['document_id'];
                $data['status'] = 'PENDING';
                $data['file_name'] = $document->name;
                $data['vendor_id'] = Auth::user()->vendor->id;
                Attachment::create($data);
            }
            User::where('id', Auth::user()->id)->update([
                'status' => 'WAITING',
            ]);

        $user = User::where('id', Auth::user()->id)->with('vendor')->first();
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200 , $user , "");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
