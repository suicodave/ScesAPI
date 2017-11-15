<?php

namespace App\Http\Controllers;

use App\Registrar;
use App\User;
use App\Role;
use App\Http\Resources\Registrar as RegistrarResource;
use App\Http\Resources\RegistrarCollection;
use Illuminate\Http\Request;


class RegistrarController extends Controller
{

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
        if ($request->has('items')) {
            $items = $request->items;
        }else{
            $items = 5;
        }
        
       
        return new RegistrarCollection(Registrar::paginate($items));
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *  store admin 
     *
     */
    public function store(Request $request)
    {
        $this->authorize('storeRegistrar',User::class);
        $request->validate([
            'processed_by' => "required|numeric",
            'email' => 'required|email|unique:users',
            'password' => 'required|min:10',
            'first_name' => 'required|max:15',
            'middle_name' => 'required|max:15',
            'last_name' => 'required|max:15'
        ]);

        $role = Role::where('name','Registrar Officer')->first();
        
        //set Registrar User Account
        $user = new User();
        $user->name =  ucwords($request->input('first_name'))." ".ucwords($request->input('last_name'));
        $user->email = $request->input('email');
        $user->role_id = $role->id;
        $user->password = bcrypt($request->input('password'));
        $user->save();

        //set Registrar entity
        $registrar = new Registrar();
        $registrar->first_name = ucwords($request->input('first_name'));
        $registrar->middle_name = ucwords($request->input('middle_name'));
        $registrar->last_name = ucwords($request->input('last_name'));
        $registrar->email = $request->input('email');
        $registrar->processed_by = $request->input('processed_by');
        
        
        //save the registrar linked with the user account .
        
        $user->registrarProfile()->save($registrar);

        //

        return response()->json([
            'message' => 'A new Registrar Officer has been created!',
            'data' => $user->registrarProfile
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Registrar  $registrar
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        $registrar = Registrar::findOrFail($id);
        return new RegistrarResource($registrar);
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Registrar  $registrar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Registrar $registrar)
    {
        $registrar->first_name = $request->input('first_name');
        $registrar->save();

        return response()->json([
            "message" => "A Registrar Officer has been updated of id $registrar->id.",
            "data" => $registrar
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Registrar  $registrar
     * @return \Illuminate\Http\Response
     * 
     */
    public function destroy(Registrar $registrar)
    {
        return response()->json([
            "message" => "A Registrar Officer has been deleted of id $registrar->id.",
            "data" => $registrar
        ]);
    }



}
