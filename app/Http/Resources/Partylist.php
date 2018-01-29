<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Partylist extends Resource
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
            'name' => $this->name,
            'is_independent' => ($this->is_indipendent == null) ? 0 : $this->is_indipendent,
            'election_id' => $this->election_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
