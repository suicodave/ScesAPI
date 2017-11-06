<?php

namespace App\Http\Controllers;

use App\Admin;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use JWTAuth;

class AdminController extends Controller
{

    public function __construct(){
        $this->middleware('jwtAuth')->except('login','checkRoleUser');
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $this->authorize('view',$user);
        $token = JWTAuth::toUser();

        $user = User::with("role")->find($token)->first();
        

      
        
        return response([
            "user" => $user,
            "is Student" => ($user->role->name == "Student" )
        ]);
    }

    public function checkRoleUser(){
        $checkExistingAdmin = Role::with("user")->where("name","Super User")->get();
        return response()->json($checkExistingAdmin);
    }
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        //
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        //
    }

    public function login(Request $request){
   
        $user = $request->only(["email","password"]);

        if(!$token=JWTAuth::attempt($user)){
            return response()->json([
                "message" => "Invalid credentials"
            ],401);
        }
        

        return response()->json($token);
    }
}
