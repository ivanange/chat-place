<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userId = Auth::getAuthIdentifier();
        $detail = $request->input('details') ?? false;
        return [
            'id' => $this->id,
            'name' => $this->name,
            $this->mergeWhen( $userId === $this->id, [
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'password' => $this->password,
                    'settings' => $this->settings,
                    'state' => $this->state,
                    'visibility' => $this->visibility,
                ]),
            'avatar' => $this->avatar,
            $this->mergeWhen($detail, [
                'desc' => $this->desc,
                'link' => $this->link,
            ]),
            
        ];
    }
}
