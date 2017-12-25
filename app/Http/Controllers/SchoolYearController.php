<?php

namespace App\Http\Controllers;

use App\SchoolYear;
use App\User;
use App\Http\Resources\SchoolYear as SchoolYearResource;
use App\Http\Resources\SchoolYearCollection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        return new SchoolYearCollection(SchoolYear::orderBy($orderBy,$orderValue)->paginate($items)->appends([
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
        
        $this->authorize('updateSchoolYear',User::class);

        $request->validate([
            'base' => [
                'required',
                'digits:4',
                Rule::unique('school_years')->ignore($schoolYear->id)
            ]
        ]);

        $base = $request->input('base');
        $endYear = $base + 1;
        $schoolYearName = "$base - $endYear";
        $oldSchoolYear = $schoolYear->name;    
        $schoolYear->base = $request->input('base');
        $schoolYear->name =$schoolYearName;
        $schoolYear->save();

        return (new SchoolYearResource($schoolYear))->additional([
            'externalMessage' => "School Year $oldSchoolYear has been updated to $schoolYear->name.",
            'internalMessage' => "School Year Updated."
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SchoolYear  $schoolYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolYear $schoolYear)
    {
        $this->authorize('deleteSchoolYear',User::class);
        if($schoolYear->is_active){
            return response()->json([
                'externalMessage' => "SchoolYear $schoolYear->name is active and cannot be deleted",
                'internalMessage' =>'Active School Year cannot be deleted'
            ]);
        }
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

        return new SchoolYearCollection(SchoolYear::onlyTrashed()->orderBy($orderBy,$orderValue)->paginate($items)->appends([
            'items' => $items,
            'orderBy' => $orderBy,
            'orderValue' => $orderValue
        ]));
       // return $this->items;
    }


    //show trashed SY
    public function showTrashed($id){

        return new SchoolYearResource(SchoolYear::onlyTrashed()->findorFail($id));
        
        
    }

    public function restore(Request $request,$id){

        $this->authorize('restoreSchoolYear',User::class);

        $restoreSubject = SchoolYear::onlyTrashed()->findOrFail($id);
        $restoreSubject->restore();
        return (new SchoolYearResource($restoreSubject))->additional([
            'externalMessage' => "School Year $restoreSubject->name has been restored.",
            'internalMessage' => "School Year restored."
        ]);
    }


    public function getActiveSchoolYear(){
        
        $getActiveSchoolYear = SchoolYear::where('is_active',true)->first();

        return new SchoolYearResource($getActiveSchoolYear);
    }

    public function activateSchoolYear(SchoolYear $schoolYear)
    {

        $this->authorize('activateSchoolYear',User::class);

        $getActiveSchoolYear = SchoolYear::where('is_active',true);
        
        $count = $getActiveSchoolYear->count();
         if ($count >= 1 ) {
            $getActiveSchoolYear->get();
            $getActiveSchoolYear->update([
                'is_active' => false
            ]);
        
         }

         $schoolYear->is_active = true;
         $schoolYear->save();

         return (new SchoolYearResource($schoolYear))->additional([
            'externalMessage' => "Active School Year is set to $schoolYear->name.",
            'internalMessage' => "School Year set active."
        ]);
        
    }



}
