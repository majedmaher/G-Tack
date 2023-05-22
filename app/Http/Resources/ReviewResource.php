<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'order_id' => $this->order_id,
            'vendor_id' => $this->vendor_id,
            'rate' => $this->rate,
            'feedback' => $this->feedback,
            'created_at' => $this->created_at,
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
