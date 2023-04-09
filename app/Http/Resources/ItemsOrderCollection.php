<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemsOrderCollection extends ResourceCollection
{
    public $collects = ItemsOrderResource::class;
}
