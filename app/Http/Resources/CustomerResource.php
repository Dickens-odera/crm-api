<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'photo_url' => $this->photo_url,
            'added_by' => new UserResource($this->owner),
            'last_updated_by' => new UserResource($this->updator)
        ];
    }
}
