<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'document_id' => $this->document_id,
            'vendor_id' => $this->vendor_id,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'document' => DocumentResource::make($this->whenLoaded('document')),
        ];
    }
}
