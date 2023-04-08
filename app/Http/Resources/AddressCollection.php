<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AddressCollection extends ResourceCollection
{
    public $collects = AddressResource::class;

    public function toArray($request)
    {
        return [
            'code' => 200,
            'status' => true,
            'data' => $this->collection,
        ];
    }
}
