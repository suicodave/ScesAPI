<?php

namespace App\Http\Resources;

use App\Department;
use Illuminate\Http\Resources\Json\Resource;
use App\Student;

class Election extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $dep_ids = explode(' ', $this->department_ids);
        $departments = Department::find($dep_ids);

        $students = Student::where('school_year_id', $this->school_year_id)
            ->whereIn('department_id', $dep_ids)->count();


        return [
            'id' => $this->id,
            'description' => $this->description,
            'number_of_students' => $students,
            'is_active' => $this->is_active,
            'is_published' => $this->is_published,
            'accumulated_votes' => 'asd',
            'remaining_votes' => '12',
            'departments' => $departments,
            'school_year' => $this->school_year,
            'is_party_enabled' => $this->is_party_enabled,
            'is_colrep_enabled' => $this->is_colrep_enabled,
            'college_representatives' => $this->when($this->is_party_enabled, $this->collegeRepresentatives),
            'partylists' => $this->when($this->is_party_enabled, route('partylists.index', $this->id)),
            'processed_by' => $this->processed_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'positions' => route('positions.index', $this->id),
            'candidates' => route('candidates.index', $this->id)
        ];
    }
}
