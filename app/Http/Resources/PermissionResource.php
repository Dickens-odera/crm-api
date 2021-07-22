<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
       return [
           'id' => $this->id,
           'name' => $this->name,
           'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
           'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
       ];
    }
}
