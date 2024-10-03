<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
                "code" => $this->whenNotNull($this->resource->code),
                "start_date" => $this->whenNotNull($this->resource->start_date),
                "end_date" => $this->whenNotNull($this->resource->end_date),
                "discount_percent" =>$this->whenNotNull($this->resource->discount),
            ],
            "success" => $this->msg
        ];
    }
}
