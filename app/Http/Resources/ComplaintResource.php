<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'customer_id' => $this->customer_id,
            'vendor_id' => $this->vendor_id,
            'order_id' => $this->order_id,
            'content' => $this->content,
            'image' => $this->image,
        ];
    }
}
