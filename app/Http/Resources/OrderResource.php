<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'number' => $this->number,
            'status' => $this->status,
            'note' => $this->note,
            'total' => $this->total,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'time' => $this->time,
            'created_at' => $this->created_at,
            'items' => new ItemsOrderCollection($this->whenLoaded('items')),
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'reviews' => new ReviewResource($this->whenLoaded('reviews')),
        ];
    }
}
