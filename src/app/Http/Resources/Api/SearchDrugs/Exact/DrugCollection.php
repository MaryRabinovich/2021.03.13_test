<?php

namespace App\Http\Resources\Api\SearchDrugs\Exact;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DrugCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => DrugResource::collection($this->collection)
        ];
    }
}
