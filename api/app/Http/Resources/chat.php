<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\chat as constants;

class chat extends JsonResource
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
            "type" => $this->type,
            $this->mergeWhen( $this->type !== constants::ONE2ONE, [
                'title' => $this->title,
                'desc' => $this->desc,
                'avatar' => $this->avatar,
                'link' => $this->link,
            ])
        ];
    }
}
