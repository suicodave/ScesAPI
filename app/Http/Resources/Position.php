<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\CandidateCollection;

class Position extends Resource
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
            'number_of_winners' => $this->number_of_winners,
            'is_colrep' => $this->is_colrep,
            'college_id' => $this->col_id,
            'election' => $this->election,
            'rank' => $this->rank,
            'candidates' => new CandidateCollection($this->candidates),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'college' => $this->when($this->is_colrep, $this->college)
        ];
    }
}
