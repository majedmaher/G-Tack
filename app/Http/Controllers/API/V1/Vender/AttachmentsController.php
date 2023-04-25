<?php

namespace App\Http\Controllers\API\V1\Vender;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\AttachmentStoreRequest;
use App\Models\Attachment;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $document = Document::where('status' , 'ACTIVE')->get();
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
        $data['vendor_id'] = Auth::user()->id;
        foreach($data['data'] as $key => $value){
            if ($attachmentStoreRequest->hasFile($value->value)) {
                $file = $attachmentStoreRequest->file($value->value);
                $fileName = time() . '_' . '.' . $file->getClientOriginalExtension();
                if($value->type == "IMAGE"){
                    $file->move('image/venders', $fileName);
                    $data['file_path'] = 'image/venders/' . $fileName;
                }else{
                    $file->move('file/users', $fileName);
                    $data['file_path'] = 'file/users/' . $fileName;
                }
            }
        $data['document_id'] = $value->id;
        $data['status'] = 'PENDING';
        Attachment::create($data);
        }
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
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
