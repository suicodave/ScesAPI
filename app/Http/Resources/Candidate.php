<?php

namespace App\Http\Resources;

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
            'student_profile' => $this->student,
            'election' => $this->election,
            'partylist' => $this->when($this->election->is_party_enabled, $this->election->is_party_enabled),
            'about_me' => $this->about_me,
            'profile_image' => $this->profile_image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at

        ];
    }
}
