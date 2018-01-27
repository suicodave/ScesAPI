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
            'departments' => $departments,
            'school_year' => $this->school_year,
            'is_party_enabled' => $this->is_party_enabled,
            'is_colrep_enabled' => $this->is_colrep_enabled,
            'college_representatives' => $this->when($this->is_party_enabled, 'asd'),
            'partylist' => $this->when($this->is_party_enabled, 'asd'),
            'processed_by' => $this->processed_by,
            'number_of_students' => $students,
            'accumulated_votes' => 'asd',
            'remaining_votes' => '12'
        ];
    }
}
