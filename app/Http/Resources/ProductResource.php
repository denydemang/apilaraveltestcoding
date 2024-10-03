<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "data" => [
                "id" => $this->whenNotNull($this->resource->id),
                "name" => $this->whenNotNull($this->resource->name),
                "category" => $this->whenNotNull($this->resource->category),
                "description" => $this->whenNotNull($this->resource->description),
                "price" =>$this->whenNotNull($this->resource->price),
            ],
            "success" => $this->msg
        ];
    }
}
