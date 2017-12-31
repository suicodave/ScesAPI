<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;
class Comelec extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->user->email ,
            'gender' => $this->gender,
            'status' => $this->status,
            'birthdate' => ($this->birthdate == null) ? null :  (new Carbon($this->birthdate))->toFormattedDateString(),
            'profile' => route('registrars.show',$this->id),
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
