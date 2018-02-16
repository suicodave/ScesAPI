<?php

namespace App\Http\Resources;

use App\Position;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VoteStandingCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private $election;
    private $is_masked;
    public function __construct($election, $is_masked)
    {
        $this->election = $election;
        $this->is_masked = $is_masked;
    }

    public function toArray($request)
    {
        $position = Position::with(['candidates' => function ($query) {
            return $query->with('student')->withCount('votes')->orderBy('votes_count', 'desc');
        }])->where('election_id', $this->election->id)->orderBy('rank', 'asc')->get();

        $map_position = $position->map(function ($item) {

            $map_students = $item['candidates']->map(function ($item) {
                return [
                    'full_name' => $item['student']['first_name'] . ' ' . $item['student']['middle_name'] . ' ' . $item['student']['last_name'],
                    'votes' => $item['votes_count']
                ];
            });

            return [
                'name' => $item['name'],
                'candidates' => $map_students
            ];
        });


        if ($this->is_masked) {
            $map_position = $position->map(function ($item) {

                $map_students = $item['candidates']->map(function ($item) {
                    return [
                        'votes' => $item['votes_count']
                    ];
                });

                return [
                    'name' => $item['name'],
                    'candidates' => $map_students
                ];
            });
        }

        return $map_position;
    }
}
