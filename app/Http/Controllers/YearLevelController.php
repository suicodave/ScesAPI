<?php

namespace App\Http\Controllers;

use App\YearLevel;
use App\Department;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\YearLevel as YearLevelResource;
use App\Http\Resources\YearLevelCollection;

class YearLevelController extends Controller
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
        return new YearLevelCollection(YearLevel::orderBy($orderBy,$orderValue)->paginate($items)->appends([
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
        $this->authorize('storeYearLevel',User::class);
        
        $request->validate([
            'name' => 'required|string|min:6|max:8',
            'department_id' => 'required|numeric'
        ]);

        $name = ucwords($request->input('name'));
        $departmentId = $request->input('department_id');

        $yearLevel = YearLevel::firstOrNew([
            'name' => $name,
            'department_id' => $departmentId
        ]);
       

        $department = Department::findOrFail($departmentId);

        $department->yearLevels()->save($yearLevel);


        return (new YearLevelResource($yearLevel))->additional([
            'externalMessage' => "New Year Level $yearLevel->name in Department of $department->name has been created.",
            'internalMessage' => 'Year Level created.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\YearLevel  $yearLevel
     * @return \Illuminate\Http\Response
     */
    public function show(YearLevel $yearLevel)
    {
        return new YearLevelResource($yearLevel);
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\YearLevel  $yearLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, YearLevel $yearLevel)
    {
        $this->authorize('updateYearLevel',User::class);
        
        $request->validate([
            'name' => 'required|string|min:6|max:8',
            'department_id' => 'required|numeric'
        ]);
        
        $name = $request->input('name');
        $departmentId = $request->input('department_id');
        
        $oldYearLevel = $yearLevel->name;
        $oldDepartment = $yearLevel->department->name;
        $yearLevel->name = $name;

        $department = Department::findOrFail($departmentId);
        
        $yearLevel->department()->associate($department);
        $yearLevel->save();
        return (new YearLevelResource($yearLevel))->additional([
            'externalMessage' => "School Year $oldYearLevel in Department of $oldDepartment has been updated to $yearLevel->name in Department of $department->name.",
            'internalMessage' => "School Year Updated."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\YearLevel  $yearLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(YearLevel $yearLevel)
    {
        $this->authorize('deleteYearLevel',User::class);
        $department = $yearLevel->department->name;
        $yearLevel->delete();
        
        return (new YearLevelResource($yearLevel))->additional([
            'externalMessage' => "Year Level $yearLevel->name in Department of $department  has been deleted.",
            'internalMessage' => 'Year Level Deleted.',
        ]);
    }

    public function trashedIndex(Request $request){
        
        $items = $request->has('items') ? $request->items : $this->items ; 

        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy ;

        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;

        return new YearLevelCollection(YearLevel::onlyTrashed()->orderBy($orderBy,$orderValue)->paginate($items)->appends([
            'items' => $items,
            'orderBy' => $orderBy,
            'orderValue' => $orderValue
        ]));
        // return $this->items;
    }
        
        
    //show trashed SY
    public function showTrashed($id){

        return new YearLevelResource(YearLevel::onlyTrashed()->findorFail($id));
        
        
    }

    public function restore(Request $request,$id){

        $this->authorize('restoreYearLevel',User::class);

        $restoreSubject = YearLevel::onlyTrashed()->findOrFail($id);
        $department = $restoreSubject->department->name;
        $restoreSubject->restore();
        return (new YearLevelResource($restoreSubject))->additional([
            'externalMessage' => "Year Level $restoreSubject->name in Department of $department has been restored.",
            'internalMessage' => "Year Level restored."
        ]);
    }
}
