<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Admin extends Resource
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
            'user_id' => $this->user_id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'status' => $this->status,
            'gender' => $this->gender,
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at
        ];
    }
}
