<?php

namespace App\Http\Controllers;

use App\Position;
use Illuminate\Http\Request;
use App\Election;
use App\Http\Resources\Position as PositionResource;
use App\Http\Resources\PositionCollection;
use JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PositionController extends Controller
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

        $position = Position::where('election_id', $election->id);
        return new PositionCollection($position->orderBy($orderBy, $orderValue)->paginate($items)->appends([
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
    public function store(Request $request, Election $election)
    {
        $this->authorize('storePosition', 'App\User');
        $request->validate([
            'name' => 'required|unique:positions|max:60',
            'number_of_winners' => 'required|numeric|digits:1',
            'is_colrep' => 'boolean'
        ]);

        $position = new Position();
        $position->name = ucwords($request->name);
        $position->number_of_winners = $request->number_of_winners;
        $position->is_colrep = $request->has('is_colrep') ? $request->is_colrep : 0;
        $election->positions()->save($position);
        return (new PositionResource($position))->additional([
            'externalMessage' => "New position has been created.",
            'internalMessage' => 'Position created.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function show(Election $election, Position $position)
    {
        if($election->id != $position->election_id){
            throw new ModelNotFoundException;
        }

        return (new PositionResource($position));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Election $election, Position $position)
    {
        $this->authorize('deletePosition', 'App\User');
        if ($position->election_id != $election->id) {
            throw new ModelNotFoundException;
        }
        $position->delete();
        return (new PositionResource($position))->additional([
            'externalMessage' => "$position->name has been deleted.",
            'internalMessage' => 'Position Deleted.',
        ]);
    }
}
