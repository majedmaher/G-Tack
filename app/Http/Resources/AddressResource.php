<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'user_name' => $this->user_name,
            'user_phone' => $this->user_phone,
            'customer_id' => $this->customer_id,
            'label' => $this->label,
            'map_address' => $this->map_address,
            'description' => $this->description,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
