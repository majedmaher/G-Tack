<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VenderResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'max_orders' => $this->max_orders,
            'max_jar' => $this->max_jar,
            'active' => $this->active,
            'governorate' => new GovernorateResource($this->whenLoaded('governorate')),
        ];
    }
}
