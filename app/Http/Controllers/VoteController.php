<?php

namespace App\Http\Controllers;

use App\Vote;
use Illuminate\Http\Request;
use App\Election;
use App\Http\Resources\Vote as VoteResource;
use App\Http\Resources\CandidateCollection;
use App\Http\Resources\VoteCollection;
use App\Student;
use App\Position;
use App\Http\Resources\VoteStandingCollection;

class VoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwtAuth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function myVotes(Election $election, Student $student)
    {
        $votes = Vote::where([
            ['election_id', $election->id],
            ['student_id', $student->id]
        ])->get();
        return (new VoteCollection($votes));
    }

    public function vote(Request $request)
    {
        $this->authorize('vote', 'App\User');

        $request->validate([
            'election_id' => 'required |exists:elections,id| numeric',
            'student_id' => 'required|unique:votes|numeric',
            'candidate_id' => 'required|array',
            'candidate.*' => 'numeric'
        ]);

        $election = Election::find($request->election_id);

        $ids = $request->candidate_id;

        foreach ($ids as $id) {
            $vote = new Vote();
            $vote->election_id = $request->election_id;
            $vote->student_id = $request->student_id;
            $vote->candidate_id = $id;
            $vote->save();
        }

        return (new VoteStandingCollection($election, true))->additional([
            'externalMessage' => "You have successfully voted!",
            'internalMessage' => 'Vote created.',
        ]);




    }

    public function getVoterStatus(Election $election)
    {
        $department_ids = explode(' ', $election->department_ids);
        $students = Student::where('school_year_id', $election->school_year_id)->whereIn('department_id', $department_ids)->withCount('votes')->orderBy('last_name', 'asc')->get();

        $map_students = $students->map(function ($student) {
            return [
                'name' => $student['last_name'] . ', ' . $student['first_name'] . ' ' . $student['last_name'],
                'vote' => $student['votes_count']
            ];
        });
        return response()->json($map_students);
    }

    public function standing(Election $election, Request $request)
    {
        $request->validate([
            'is_masked' => 'boolean'
        ]);


        return (new VoteStandingCollection($election, $request->is_masked));
    }


}
