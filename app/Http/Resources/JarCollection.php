<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class JarCollection extends ResourceCollection
{
    public $collects = JarResource::class;

    public function toArray($request)
    {
        return [
            'code' => 200,
            'status' => true,
            'data' => $this->collection,
        ];
    }
}
