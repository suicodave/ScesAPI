<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

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
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'profile' => route('registrars.show',$this->id),
            'avatar' => route('users.image',$this->profile_image),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'relationshipLinks' => [
                'registeredStudents' => 'registrars/id/students?token='.$request->token,
                'activityLogs' => 'registrars/id/logs?token='.$request->token 
            ]
        ];
    }
    
    
}
