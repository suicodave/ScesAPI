<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class Student extends Resource
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
            'user_id'=> $this->user->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->user->email ,
            'gender' => $this->gender,
            'birthdate' => ($this->birthdate == null) ? null :  (new Carbon($this->birthdate))->toFormattedDateString(),
            'home_address' => $this->home_address,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'department' => $this->department,
            'year_level' => $this->year_level,
            'college' => $this->college,
            'school_year' => $this->school_year,
            'avatar' => route('users.image',$this->profile_image),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'processed_by' => $this->processor,
            'relationshipLinks' => [
                'registeredElection' => 'registrars/id/students',
                'activityLogs' => 'registrars/id/logs'
            ]
        ];
    }
}
