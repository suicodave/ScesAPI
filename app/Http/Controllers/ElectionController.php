<?php

namespace App\Http\Controllers;

use App\Election;
use App\Department;
use App\Http\Resources\Election as ElectionResource;
use App\Http\Resources\ElectionCollection;
use Illuminate\Http\Request;
use JWTAuth;
use App\Partylist;
use App\College;
use App\Position;
use App\Candidate;

class ElectionController extends Controller
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
    public function index(Request $request)
    {
        $request->validate([
            'is_published' => 'boolean',
            'is_active' => 'boolean',
            'my_election' => 'boolean'
        ]);
        $election = Election::when($request->filled('is_published'), function ($query) use ($request) {
            $is_published = $request->is_published;
            return $query->where('is_published', $is_published);
        })->when($request->filled('is_active'), function ($query) use ($request) {
            $is_active = $request->is_active;
            return $query->where('is_active', $is_active);
        });

        if ($request->my_election) {
            $user = JWTAuth::toUser();
            $election->where('processor_id', $user->id);
        }

        $items = $request->has('items') ? $request->items : $this->items;
        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy;
        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;
        return new ElectionCollection($election->orderBy($orderBy, $orderValue)->paginate($items)->appends([
            'items' => $items,
            'orderBy' => $orderBy,
            'orderValue' => $orderValue
        ]));


    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('storeElection', 'App\User');

        $request->validate([
            'department_ids' => 'required|array',
            'description' => 'required|max:200',
            'school_year_id' => 'required|numeric',
            'is_party_enabled' => 'boolean',
            'is_colrep_enabled' => 'boolean'
        ]);

        $election = new Election();
        $election->description = ucfirst($request->description);
        $election->school_year_id = $request->school_year_id;
        $election->department_ids = implode($request->department_ids, " ");
        $election->is_party_enabled = $request->is_party_enabled || 0;
        $election->is_colrep_enabled = $request->is_colrep_enabled || 0;

        $processed_by = JWTAuth::toUser();
        $election->processor_id = $processed_by->id;
        $election->save();

        if ($election->is_party_enabled) {
            $party = new Partylist();
            $party->name = "Independent";
            $party->is_independent = true;
            $election->partylists()->save($party);
        }

        if ($election->is_colrep_enabled) {
            $colleges = College::all();
            foreach ($colleges as $key) {
                $colpos = new Position();
                $colpos->name = "College of " . $key->name . " Representative";
                $colpos->is_colrep = true;
                $colpos->col_id = $key->id;
                $election->positions()->save($colpos);
            }
        }

        return (new ElectionResource($election))->additional([
            'externalMessage' => "New election has been created.",
            'internalMessage' => 'Election created.',
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Election  $election
     * @return \Illuminate\Http\Response
     */
    public function show(Election $election)
    {
        return (new ElectionResource($election));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Election  $election
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Election $election)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Election  $election
     * @return \Illuminate\Http\Response
     */
    public function destroy(Election $election)
    {
        $this->authorize('deleteElection', 'App\User');


        $election->delete();

        $candidate = Candidate::where('election_id', $election->id);
        $candidate->delete();
        $position = Position::where('election_id', $election->id);
        $position->delete();
        $party = Partylist::where('election_id', $election->id);
        $party->delete();

        return (new ElectionResource($election))->additional([
            'externalMessage' => "Election has been deleted.",
            'internalMessage' => 'Election Deleted.',
        ]);
    }



}
