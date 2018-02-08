<?php

namespace App\Http\Resources;

use App\Http\Resources\Student as StudentResource;
use Illuminate\Http\Resources\Json\Resource;

class Candidate extends Resource
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
            'student_profile' => new StudentResource($this->student),
            'election' => $this->election,
            'position' => $this->position,
            'partylist' => $this->when($this->election->is_party_enabled, $this->partylist),
            'about_me' => $this->about_me,
            'profile_image' => $this->profile_image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at

        ];
    }
}
