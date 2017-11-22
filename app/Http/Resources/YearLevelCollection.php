<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class YearLevelCollection extends ResourceCollection
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
            'data' => $this->collection,
            'additionalParams' => [
                'items' => ['numeric', 'default:15'],
                'order' => [
                    'default' => [
                        'orderBy' => 'id',
                        'value' => 'desc'
                    ],
                    'orderBy' => 'fields' ,
                    'orderValue' => ['desc','asc']
                ]
            ],
            'fields' => [
                'id',
                'department_id',
                'name',
                'created_at',
                'updated_at'
            ]
        ];
    }
}
