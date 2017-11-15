<?php

namespace App\Http\Controllers;

use App\SchoolYear;
use App\User;
use App\Http\Resources\SchoolYear as SchoolYearResource;
use Illuminate\Http\Request;

use JWTAuth;

class SchoolYearController extends Controller
{

    public function __construct(){
        $this->middleware('jwtAuth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'year' => 'required|max:4|min:4|'
        ]);

        $yearStart = $request->input('year');

        $yearEnd = $yearStart +1;

        $stringSchoolYear = "$yearStart - $yearEnd";

        $schoolYear = new SchoolYear();
        $schoolYear->name = $stringSchoolYear;
        $schoolYear->save();      
        
        return (new SchoolYearResource($schoolYear))->additional([
            'externalMessage' => 'New School Year has been created.',
            'internalMessage' => 'School Year created.',
            'profile' => route('school_years.show',$schoolYear->id),
            'otherMethods' =>[
                'POST' => [
                    'link' => route('school_years.store'),
                    'params' => ['name'],
                    'purpose' => 'Create new School Year'
                ],
                'PUT' =>[
                    'link' => route('school_years.show',$schoolYear->id),
                    'params' => ['name'],
                    'purpose' => 'Update  School Year'
                ],
                "DELETE" => [
                    'link' => route('school_years.show',$schoolYear->id),
                    'purpose' => 'Delete  School Year'
                ]

            ]

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
        //
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
        //
    }
}
