<?php

namespace App\Http\Resources\Api\SearchDrugs\Exact;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\SearchDrugs\SubstanceResource;

class DrugResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'substances_count' => $this->substances->count(),
            'substances' => SubstanceResource::collection($this->whenLoaded('substances'))
        ];
    }
}
