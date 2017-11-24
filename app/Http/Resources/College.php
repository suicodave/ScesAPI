<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class College extends Resource
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
            'head' => $this->head,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->when($this->deleted_at,$this->deleted_at)
        ];
    }
    public function with($request){
        return [
            'profile' => route('colleges.show',$this->id),
            'otherMethods' =>[
                'POST' => [
                    'link' => route('colleges.store'),
                    'params' => ['name:string','head:string'],
                    'purpose' => 'Create new College'
                ],
                'PUT' =>[
                    [
                        'link' => route('colleges.show',$this->id),
                        'params' => ['name:string','head:string'],
                        'purpose' => 'Update  College'
                    ],

                    [
                        'link' => route('colleges.restore',$this->id),
                        'purpose' => 'Restore deleted College'
                    ]
                ],
                "DELETE" => [
                    'link' => route('colleges.show',$this->id),
                    'purpose' => 'Delete  College'
                ],
                'GET' => [
                    [
                        'link' => route('colleges.trashed'),
                        'purpose' => 'List of deleted College'
                    ],
                    [
                        'link' => route('colleges.trashed.show',$this->id),
                        'purpose' => 'Show deleted College'
                    ],
                ]
                

            ]
        ];
    }
    
        
}
