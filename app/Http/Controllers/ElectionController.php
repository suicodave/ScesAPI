<?php

namespace App\Http\Controllers;

use App\Election;
use App\Department;
use App\Http\Resources\Election as ElectionResource;
use App\Http\Resources\ElectionCollection;
use Illuminate\Http\Request;
use JWTAuth;


class ElectionController extends Controller
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
        $election = Election::all();
        return (new ElectionCollection($election));
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
        //
    }
}
