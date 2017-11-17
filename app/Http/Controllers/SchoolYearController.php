<?php

namespace App\Http\Controllers;

use App\SchoolYear;
use App\User;
use App\Http\Resources\SchoolYear as SchoolYearResource;
use App\Http\Resources\SchoolYearCollection;
use Illuminate\Http\Request;

use JWTAuth;

class SchoolYearController extends Controller
{

    private $items = 15;
    private $orderBy = 'id';
    private $orderValue = 'desc';

    public function __construct(){
        $this->middleware('jwtAuth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = $request->has('items') ? $request->items : $this->items ; 
        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy ;
        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;
        return new SchoolYearCollection(SchoolYear::orderBy($orderBy,$orderValue)->paginate($items));
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('storeSchoolYear',User::class);

        $request->validate([
            'base' => 'required|unique:school_years|numeric|digits:4|max:2200'
        ]);

        $yearStart = $request->input('base');

        $yearEnd = $yearStart +1;

        $stringSchoolYear = "$yearStart - $yearEnd";

        $schoolYear = new SchoolYear();
        $schoolYear->base = $yearStart;
        $schoolYear->name = $stringSchoolYear;
        $schoolYear->save();      
        
        return (new SchoolYearResource($schoolYear))->additional([
            'externalMessage' => 'New School Year has been created.',
            'internalMessage' => 'School Year created.',
            

        ]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolYear $schoolYear)
    {
        return new SchoolYearResource($schoolYear);
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SchoolYear $schoolYear)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolYear $schoolYear)
    {
        $schoolYear->delete();
        return (new SchoolYearResource($schoolYear))->additional([
            'externalMessage' => "School Year $schoolYear->name has been deleted.",
            'internalMessage' => 'School Year Deleted.',
        ]);
    }

    //trashed index
    public function trashedIndex(Request $request){

        $items = $request->has('items') ? $request->items : $this->items ; 

        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy ;

        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;

        return new SchoolYearCollection(SchoolYear::onlyTrashed()->orderBy($orderBy,$orderValue)->paginate($items));
       // return $this->items;
    }


    //show trashed SY
    public function showTrashed($id){
        
        return response()->json(
            SchoolYear::onlyTrashed()->findorFail($id)
        );
    }

    public function restore(Request $request,$id){
        $restoreSubject = SchoolYear::onlyTrashed()->findOrFail($id);
        $restoreSubject->restore();
        return (new SchoolYearResource($restoreSubject))->additional([
            'externalMessage' => "School Year $restoreSubject->name has been restored.",
            'internalMessage' => "School Year restored."
        ]);
    }
}
