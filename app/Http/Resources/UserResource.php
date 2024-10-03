<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    protected $acccesToken;
    protected $refreshToken;

    public function __construct($resource, $msg, $acccesToken = null, $refreshToken = null)
    {
        parent::__construct($resource, $msg);
        $this->acccesToken = $acccesToken;
        $this->refreshToken = $refreshToken;

    }

    public function toArray(Request $request): array
    {
        return [
            "data" => [
                "username" => $this->resource->username,
                "email" => $this->resource->email,
                "access_token" => $this->whenNotNull($this->acccesToken),
                "token_type" =>$this->whenNotNull($this->when($this->acccesToken , "bearer", null)),
                "expired_in" => $this->whenNotNull($this->when($this->acccesToken , Auth::factory()->getTTL() . "Minutes" , null)),
            ],
            "success" => $this->msg
        ];
    }
}
