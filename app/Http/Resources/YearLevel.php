<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class YearLevel extends Resource
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
            'name' => $this->name,
            'department' => $this->department,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->when($this->deleted_at,$this->deleted_at)
        ];
    }
    public function with($request){
        return [
            'profile' => route('year_levels.show',$this->id),
            'otherMethods' =>[
                'POST' => [
                    'link' => route('year_levels.store'),
                    'params' => ['name:alphanumeric','department_id:neumeric'],
                    'purpose' => 'Create new Year Level'
                ],
                'PUT' =>[
                    [
                        'link' => route('year_levels.show',$this->id),
                        'params' => ['name:alphanumeric','department_id:neumeric'],
                        'purpose' => 'Update  Year Level'
                    ],

                    [
                        'link' => route('year_levels.restore',$this->id),
                        'purpose' => 'Restore deleted Year Level'
                    ]
                ],
                "DELETE" => [
                    'link' => route('year_levels.show',$this->id),
                    'purpose' => 'Delete  Year Level'
                ],
                'GET' => [
                    [
                        'link' => route('year_levels.trashed'),
                        'purpose' => 'List of deleted Year Level'
                    ],
                    [
                        'link' => route('year_levels.trashed.show',$this->id),
                        'purpose' => 'Show deleted Year Level'
                    ],
                ]
                

            ]
        ];
    }
}
