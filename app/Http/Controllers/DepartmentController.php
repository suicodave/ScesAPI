<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;
use App\Http\Resources\DepartmentCollection;
use App\Http\Resources\Department as DepartmentResource;
class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $items = 15;
    private $orderBy = 'id';
    private $orderValue = 'desc';
    
    public function __construct(){
        $this->middleware('jwtAuth');
    }
    public function index()
    {
        $department=Department::with('yearLevels')->get();
        return new DepartmentCollection($department);
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $department = new Department();
        $department->name = $request->name;
        $department->save();

        return response()->json($department);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json($id);
    }

    public function showYearLevels(){
        $departments = Department::with('yearLevels')->get();
        return response()->json($departments);
    }
}
