<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'reviews_count' => intval($this->reviews_count),
            'reviews_sum_rate' => intval($this->reviews_sum_rate) / intval($this->reviews_count),
            // 'orders_count' => $this->orders_count,
            'orders_sum_time' => $this->orders_sum_time / $this->orders_avg_time,
            // 'orders_avg_time' => $this->orders_avg_time,
            'user' => new UserResource($this->whenLoaded('user')),
            'governorate' => new GovernorateResource($this->whenLoaded('governorate')),
            'region' => new GovernorateResource($this->whenLoaded('region')),
        ];
    }
}
