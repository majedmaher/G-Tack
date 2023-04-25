<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LayoutCollection extends ResourceCollection
{
    public $collects = LayoutResource::class;

    public function toArray($request)
    {
        return [
            'code' => 200,
            'status' => true,
            'data' => $this->collection,
        ];
    }
}
