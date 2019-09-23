<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class message extends JsonResource
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
            "id" => $this->id,
            "type" => $this->type,
            "text" => $this->text,
            "file" => $this->file,
            "uid" => $this->pivot->uid,
            "state" => $this->pivot->state,
            "time" => $this->pivot->time,
        ];
    }
}
