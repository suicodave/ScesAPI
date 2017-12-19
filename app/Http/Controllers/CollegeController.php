<?php

namespace App\Http\Controllers;

use App\College;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\College as CollegeResource;
use App\Http\Resources\CollegeCollection;
use Illuminate\Validation\Rule;
class CollegeController extends Controller
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
       return new CollegeCollection(College::orderBy($orderBy,$orderValue)->paginate($items)->appends([
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
        $this->authorize('storeCollege',User::class);
        $request->validate([
            'name' => 'required|unique:colleges|string|max:60|min:4',
            'head' => 'nullable|max:20|min:2'
        ]);
        
        $name = $request->input('name');
        $head = $request->input('head');
        $college = new College();
        $college->name = ucwords($name);
        $college->head = $head;
        $college->save();
        
        return (new CollegeResource($college))->additional([
            'externalMessage' => "College $college->name has been created.",
            'internalMessage' => 'College created.',
        ]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\College  $college
     * @return \Illuminate\Http\Response
     */
    public function show(College $college)
    {
        return new CollegeResource($college);
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\College  $college
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, College $college)
    {
        $this->authorize('updateCollege',User::class);
                
        $request->validate([
            'name' => [
                'required',
                'max:60',
                'min:4',
                Rule::unique('colleges')->ignore($college->id)
            ],
            'head' => [
                'required',
                'max:30',
                'min:4',
            ] 
        ]);
        
        $oldname = $college->name;
        $name = ucwords($request->input('name'));
        $head = ucwords($request->input('head'));
        
        $college->name = $name;
        $college->head = $head;
                
        $college->save();
        return (new CollegeResource($college))->additional([
            'externalMessage' => "College $oldname has been updated to $college->name headed by $college->head.",
            'internalMessage' => "College Updated."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\College  $college
     * @return \Illuminate\Http\Response
     */
    public function destroy(College $college)
    {
        $this->authorize('deleteCollege',User::class);
        $college->delete();
                
        return (new CollegeResource($college))->additional([
            'externalMessage' => "$college->name has been deleted.",
            'internalMessage' => 'College Deleted.',
        ]);
    }

    public function trashedIndex(Request $request){
            
        $items = $request->has('items') ? $request->items : $this->items ; 
    
        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy ;
    
        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;
    
        return new CollegeCollection(College::onlyTrashed()->orderBy($orderBy,$orderValue)->paginate($items)->appends([
            'items' => $items,
            'orderBy' => $orderBy,
            'orderValue' => $orderValue
        ]));
            
    }
            
            
        
    public function showTrashed($id){
    
        return new CollegeResource(College::onlyTrashed()->findorFail($id));
            
    }
    
    public function restore(Request $request,$id){
    
        $this->authorize('restoreCollege',User::class);
    
        $restoreSubject = College::onlyTrashed()->findOrFail($id);
        $restoreSubject->restore();
        return (new CollegeResource($restoreSubject))->additional([
            'externalMessage' => "$restoreSubject->name has been restored.",
            'internalMessage' => "College restored."
        ]);
    }
}


