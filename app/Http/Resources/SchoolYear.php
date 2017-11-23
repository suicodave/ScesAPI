<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SchoolYear extends Resource
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
            'base' => $this->base,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->when($this->deleted_at,$this->deleted_at),
            'is_active' => $this->when($this->is_active,$this->is_active)
        ];
    }

    public function with($request){
        return [
            'profile' => route('school_years.show',$this->id),
            'otherMethods' =>[
                'POST' => [
                    'link' => route('school_years.store'),
                    'params' => ['base'],
                    'purpose' => 'Create new School Year'
                ],
                'PUT' =>[
                    [
                        'link' => route('school_years.show',$this->id),
                        'params' => ['base'],
                        'purpose' => 'Update  School Year'
                    ],

                    [
                        'link' => route('school_years.restore',$this->id),
                        'purpose' => 'Restore deleted School Year'
                    ],
                    [
                        'link' => route('school_years.active.activate',$this->id),
                        'purpose' => 'Set active School Year'
                    ],
                ],
                "DELETE" => [
                    'link' => route('school_years.show',$this->id),
                    'purpose' => 'Delete  School Year'
                ],
                'GET' => [
                    [
                        'link' => route('school_years.trashed'),
                        'purpose' => 'List of deleted School Year'
                    ],
                    [
                        'link' => route('school_years.trashed.show',$this->id),
                        'purpose' => 'Show deleted School Year'
                    ],
                    [
                        'link' => route('school_years.active.show'),
                        'purpose' => 'Get active School Year'
                    ]
                ]
                

            ]
        ];
    }
}
