<?php

namespace App\Http\Controllers;

use App\Registrar;
use App\User;
use App\Role;
use App\Http\Resources\Registrar as RegistrarResource;
use App\Http\Resources\RegistrarCollection;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;



class RegistrarController extends Controller
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
        return new RegistrarCollection(Registrar::with('user')->orderBy($orderBy,$orderValue)->paginate($items)->appends([
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
        
        $processedby = JWTAuth::toUser();
        $registrar->processed_by = $processedby->id;
        
        
        //save the registrar linked with the user account .
        
        $user->registrarProfile()->save($registrar);

        //
        return (new RegistrarResource($registrar))->additional([
            'externalMessage' => "New Registrar has been created.",
            'internalMessage' => 'Registrar created.',
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
        

        $this->authorize('updateRegistrar',User::class);
        
        
        $request->validate([
            'email' => [
                'required',
                Rule::unique('users')->ignore($registrar->user->id)
            ],
            'status' => 'nullable|min:6|max:10',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|min:4|max:6'
        ]);
                
        
        $registrar->status = ucwords($request->input('status'));
        $registrar->birthdate = new Carbon($request->input('birthdate')); 
        $registrar->gender = ucwords($request->input('gender'));
        $user = User::findOrFail($registrar->user->id);
        $user->email = $request->input('email');
     
                
        $registrar->user()->associate($user);
        $registrar->save();

        return (new RegistrarResource($registrar))->additional([
            'externalMesage' => "Registrar has been successfully updated.",
            'internalMessage' => "Registrar Updated."
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
        $this->authorize('deleteRegistrar',User::class);

        $account = User::find($registrar->user->id);

        $registrar->delete();

        $account->delete();

        return (new RegistrarResource($registrar))->additional([
            'externalMessage' => "$registrar->first_name $registrar->last_name has been deleted.",
            'internalMessage' => 'Registrar Deleted.'
        ]);
    }

   
    public function registeredStudents($id){
        return "students";
    }

    public function trashedIndex(Request $request){
            
        $items = $request->has('items') ? $request->items : $this->items ; 
    
        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy ;
    
        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;
    
        return new RegistrarCollection(Registrar::with(['user'=>function($q){
            $q->onlyTrashed();
        }])->onlyTrashed()->orderBy($orderBy,$orderValue)->paginate($items)->appends([
            'items' => $items,
            'orderBy' => $orderBy,
            'orderValue' => $orderValue
        ]));
            
    }
            
            
        
    public function showTrashed($id){
    
        return new RegistrarResource(Registrar::with(['user'=>function($q){
            $q->withTrashed();
        }])->onlyTrashed()->findorFail($id));

        
        
    }
    
    public function restore(Request $request,$id){
    
        $this->authorize('restoreRegistrar',User::class);
    
        $restoreSubject = Registrar::onlyTrashed()->findOrFail($id);
        
        $restoreAccount = User::onlyTrashed()->findOrFail($restoreSubject->user_id);
        
        $restoreSubject->restore();
        $restoreAccount->restore();

        return (new RegistrarResource($restoreSubject))->additional([
            'externalMessage' => "$restoreSubject->first_name $restoreSubject->last_name has been restored.",
            'internalMessage' => "Registrar restored.",
         
        ]);
    }



}


    