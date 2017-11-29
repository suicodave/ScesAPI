<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class Registrar extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'middleName' => $this->middle_name,
            'lastName' => $this->last_name,
            'email' => $this->user->email ,
            'gender' => $this->gender,
            'status' => $this->status,
            'birthdate' => ($this->birthdate == null) ? null :  (new Carbon($this->birthdate))->toFormattedDateString(),
            'profile' => route('registrars.show',$this->id),
            'avatar' => route('users.image',$this->profile_image),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'processedBy' => $this->processor,
            'relationshipLinks' => [
                'registeredStudents' => 'registrars/id/students',
                'activityLogs' => 'registrars/id/logs'
            ]
        ];
    }
    
    
}
