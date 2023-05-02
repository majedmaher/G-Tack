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
            'customer_id' => $this->customer_id,
            'vendor_id' => $this->vendor_id,
            'rate' => $this->rate,
            'feedback' => $this->feedback,
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
