<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerCollection extends ResourceCollection
{
    public $collects = CustomerResource::class;

    // public function toArray($request)
    // {
    //     return [
    //         // 'code' => 200,
    //         // 'status' => true,
    //         'data' => $this->collection,
    //     ];
    // }
}
