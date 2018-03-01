<?php

namespace App\Http\Controllers;

use App\Candidate;
use Illuminate\Http\Request;
use App\Election;
use App\Position;
use App\Partylist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Student;
use Cloudinary\Uploader;
use App\Jobs\UploadImage;
use App\Http\Resources\Candidate as CandidateResource;
use App\Http\Resources\CandidateCollection;
use JWT;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    private $items = 15;
    private $orderBy = 'id';
    private $orderValue = 'desc';
    public function __construct()
    {
        $this->middleware('jwtAuth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Election $election, Request $request)
    {
        $items = $request->has('items') ? $request->items : $this->items;
        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy;
        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;

        $candidate = Candidate::where('election_id', $election->id)->orderBy('id', 'desc');
        return (new CandidateCollection($candidate->get()));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Election $election)
    {

        $this->authorize('storeCandidate', 'App\User');
        $request->validate([
            'student_id' => [
                'required',
                'filled',
                'numeric',
                Rule::unique('candidates')->where(function ($query) use ($election) {
                    return $query->where('election_id', $election->id);
                })
            ],
            'election_id' => 'required|filled|numeric',
            'position_id' => 'required|filled|numeric',
            'partylist_id' => 'filled|numeric',
            'profile_image' => 'required|filled|image',
            'about_me' => 'required|max:180',

        ]);
        $check_student = Student::findOrFail($request->student_id);
        $candidate = new Candidate();
        $candidate->about_me = $request->about_me;
        $candidate->election_id = $election->id;
        $candidate->student_id = $check_student->id;

        $image = $request->file('profile_image');




        $check_position = Position::where([
            ['id', $request->position_id],
            ['election_id', $election->id]
        ]);

        if (!$check_position->exists()) {
            throw new ModelNotFoundException;

        }
        $position = $check_position->first();
        $candidate->position_id = $position->id;


        // if election enables partylist
        if ($election->is_party_enabled) {


            $check_partylist = Partylist::where([
                ['id', $request->partylist_id],
                ['election_id', $election->id]
            ]);
            
            // if party and position not found in election
            if (!$check_partylist->exists()) {
                throw new ModelNotFoundException;
            }

            $partylist = $check_partylist->first();


            $candidate->partylist_id = $partylist->id;
            

            // get candidate count in position by party
            $candidate_count = Candidate::where([
                ['partylist_id', $candidate->partylist_id],
                ['position_id', $candidate->position_id]
            ])->count();

            // check if candidate exceeds position slots per party and not in independent 
            if ($candidate_count >= $position->number_of_winners && !$partylist->is_independent) {
                return response()->json([
                    'externalMessage' => 'Max candidates reached in position ' . $position->name . ' of party ' . $partylist->name,
                    'internalMessage ' => ' Max candidates in position by party'
                ], 422);
            }


        }

        $candidate->save();

        $this->dispatch(new UploadImage($image, $candidate));

        return (new CandidateResource($candidate))->additional([
            'externalMessage' => "New candidate has been created.",
            'internalMessage' => 'Candidate created.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function show(Election $election, Candidate $candidate)
    {

        if ($election->id != $candidate->election_id) {
            throw new ModelNotFoundException;
        }

        return (new CandidateResource($candidate));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Candidate $candidate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Election $election, Candidate $candidate)
    {
        if ($election->id != $candidate->election_id) {
            throw new ModelNotFoundException;
        }

        $this->authorize('deleteCandidate', 'App\User');
        $candidate->delete();

        return (new CandidateResource($candidate))->additional([
            'externalMessage' => "A candidate has been deleted.",
            'internalMessage' => 'Candidate Deleted.',
        ]);
    }
}
