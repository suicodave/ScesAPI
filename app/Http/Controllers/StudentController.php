<?php

namespace App\Http\Controllers;

use App\Student;
use App\User;
use App\Department;
use App\College;
use App\YearLevel;
use App\SchoolYear;
use App\Role;
use App\Http\Resources\Student as StudentResource;
use App\Http\Resources\StudentCollection;
use JWTAuth;
use Carbon\Carbon;

use Illuminate\Http\Request;

class StudentController extends Controller
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

        $student = Student::when($request->filled('school_year_id'), function ($query) use ($request) {
            $school_year_id = $request->school_year_id;
            return $query->where('school_year_id', $school_year_id);
        })->when($request->filled('department_id'), function ($query) use ($request) {
            $department_id = $request->department_id;
            return $query->where('department_id', $department_id);
        })->when($request->filled('search'), function ($query) use ($request) {
            return Student::search($request->search);
        });

        $items = $request->has('items') ? $request->items : $this->items;
        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy;
        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;

        return new StudentCollection($student->orderBy($orderBy, $orderValue)->paginate($items)->appends([
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
        $this->authorize('storeStudent', User::class);
        $validate = $request->validate([
            'school_year_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'college_id' => 'required|numeric',
            'year_level_id' => 'required|numeric',
            'first_name' => 'required|min:2|max:20',
            'middle_name' => 'required|min:2|max:20',
            'last_name' => 'required|min:2|max:20',
            'email' => 'required|email|unique:users',
            'mother_name' => 'required|max:60',
            'father_name' => 'required|max:60',
            'gender' => 'required|max:6',

            'home_address' => 'required|max:180',
            'birthdate' => 'required|date'

        ]);
        $first_name = $request->input('first_name');
        $middle_name = $request->input('middle_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $mother_name = $request->input('mother_name');
        $father_name = $request->input('father_name');
        $gender = $request->input('gender');
        $home_address = $request->input('home_address');
        $birthdate = new Carbon($request->input('birthdate'));;
        $department_id = $request->input('department_id');
        $year_level_id = $request->input('year_level_id');
        $college_id = $request->input('college_id');
        $school_year_id = $request->input('school_year_id');

        $student_exist = Student::where([
            ['first_name', '=', $first_name],
            ['middle_name', '=', $middle_name],
            ['last_name', '=', $last_name]
        ]);

        if ($student_exist->exists()) {
            return response()->json([
                'external_message' => 'Student already exists. Please check the records',
                'internal_message' => 'Student record found'
            ], 422);
        }

        //get student role
        $role = Role::where('name', 'Student')->first();

        //set student entity
        $student = new Student();
        $student->first_name = ucwords($first_name);
        $student->middle_name = ucwords($middle_name);
        $student->last_name = ucwords($last_name);
        $student->mother_name = ucwords($mother_name);
        $student->father_name = ucwords($father_name);
        $student->gender = ucwords($gender);
        $student->home_address = ucwords($home_address);
        $student->birthdate = $birthdate;
        $student->department_id = $department_id;
        $student->year_level_id = $year_level_id;
        $student->college_id = $college_id;
        $student->school_year_id = $school_year_id;
        $processor = JWTAuth::toUser();
        $student->processed_by = $processor->id;   

        //set student user account
        $user = new User();
        $user->name = $first_name . " " . $last_name;
        $user->email = $email;
        $user->password = bcrypt($first_name . $middle_name . $last_name);
        $user->role_id = $role->id;
        $user->save();

        $user->studentProfile()->save($student);


        return (new StudentResource($student))->additional([
            'externalMessage' => "New Student has been created.",
            'internalMessage' => 'Student created.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(student $student)
    {
        return new StudentResource($student);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, student $student)
    {

        $this->authorize('updateStudent', User::class);

        $request->validate([
            'department_id' => 'required|numeric',
            'college_id' => 'required|numeric',
            'year_level_id' => 'required|numeric',
            'school_year_id' => 'required|numeric'
        ]);

        $student->department_id = $request->input('department_id') ;
        $student->college_id = $request->input('college_id') ;
        $student->year_level_id = $request->input('year_level_id') ;
        $student->school_year_id = $request->input('school_year_id') ;

        $student->save();

        return (new StudentResource($student))->additional([
            'externalMessage' => "Student has been successfully updated.",
            'internalMessage' => "Student Updated."
        ]);
    }


    public function destroy(Student $student)
    {
        $this->authorize('deleteStudent', User::class);

        $processor = JWTAuth::toUser();

        $student->processed_by = $processor->id;

        $student->save();

        $account = User::find($student->user->id);
        $student->delete();

        $account->delete();



        return (new StudentResource($student))->additional([
            'externalMessage' => "Student $student->first_name $student->last_name has been deleted.",
            'internalMessage' => 'Student Deleted.',

        ]);
    }

    public function trashedIndex(Request $request)
    {

        $items = $request->has('items') ? $request->items : $this->items;

        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy;

        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;

        return new StudentCollection(Student::with(['user' => function ($q) {
            $q->onlyTrashed();
        }])->onlyTrashed()->orderBy($orderBy, $orderValue)->paginate($items)->appends([
            'items' => $items,
            'orderBy' => $orderBy,
            'orderValue' => $orderValue
        ]));

    }

    public function showTrashed($id)
    {

        return new StudentResource(Student::with(['user' => function ($q) {
            $q->withTrashed();
        }])->onlyTrashed()->findorFail($id));

    }

    public function restore(Request $request, $id)
    {

        $this->authorize('restoreStudent', User::class);

        $restoreSubject = Student::onlyTrashed()->findOrFail($id);

        $restoreAccount = User::onlyTrashed()->findOrFail($restoreSubject->user_id);

        $processor = JWTAuth::toUser();

        $restoreSubject->restore();

        $restoreSubject->processed_by = $processor->id;

        $restoreSubject->save();

        $restoreAccount->restore();

        return (new StudentResource($restoreSubject))->additional([
            'externalMessage' => "Student $restoreSubject->first_name $restoreSubject->last_name has been restored.",
            'internalMessage' => "Student restored.",

        ]);
    }


}
