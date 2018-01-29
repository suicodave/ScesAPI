<?php

namespace App\Http\Controllers;

use App\Partylist;
use Illuminate\Http\Request;
use App\Election;
use App\Http\Resources\Partylist as PartylistResource;
use App\Http\Resources\PartylistCollection;
use JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PartylistController extends Controller
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
        $partylist = Partylist::where('election_id', $election->id);
        return new PartylistCollection($partylist->orderBy($orderBy, $orderValue)->paginate($items)->appends([
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
        $this->authorize('storePartylist', 'App\User');

        $request->validate([
            'name' => 'required|unique:partylists|max:60'
        ]);

        $party = new Partylist();
        $party->name = ucwords($request->name);

        $election->partylists()->save($party);

        return (new PartylistResource($party))->additional([
            'externalMessage' => "New partylist has been created.",
            'internalMessage' => 'Partylist created.',
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Partylist  $partylist
     * @return \Illuminate\Http\Response
     */
    public function show(Election $election, Partylist $partylist)
    {

        if ($partylist->election_id != $election->id) {
            throw new ModelNotFoundException;
        }

        return new PartylistResource($partylist);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Partylist  $partylist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Partylist $partylist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Partylist  $partylist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Election $election, Partylist $partylist)
    {

        $this->authorize('deletePartylist', 'App\User');
        if ($partylist->election_id != $election->id) {
            throw new ModelNotFoundException;
        }

        $partylist->delete();

        return (new PartylistResource($partylist))->additional([
            'externalMessage' => "$partylist->name has been deleted.",
            'internalMessage' => 'Partylist Deleted.',
        ]);
    }
}
