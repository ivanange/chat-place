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
            "id" => $this->id,
            "type" => $this->type,
            $this->mergeWhen( $this->type !== constants::ONE2ONE, [
                'title' => $this->title,
                'avatar' => $this->avatar,
                $this->mergeWhen( $request->details, [
                    'desc' => $this->desc,
                    'link' => $this->link,
                ])
            ])
        ];
    }
}
