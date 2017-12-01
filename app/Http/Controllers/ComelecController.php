<?php

namespace App\Http\Controllers;

use App\Comelec;
use App\User;
use App\Role;
use App\Http\Resources\Comelec as ComelecResource;
use App\Http\Resources\ComelecCollection;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ComelecController extends Controller
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
        return new ComelecCollection(Comelec::with('user')->orderBy($orderBy,$orderValue)->paginate($items)->appends([
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
        $this->authorize('storeComelec',User::class);
        $request->validate([
            
            'email' => 'required|email|unique:users',
            'password' => 'required|min:10',
            'first_name' => 'required|max:15',
            'middle_name' => 'required|max:15',
            'last_name' => 'required|max:15'
        ]);

        $role = Role::where('name','Comelec Officer')->first();
        
        //set Comelec User Account
        $user = new User();
        $user->name =  ucwords($request->input('first_name'))." ".ucwords($request->input('last_name'));
        $user->email = $request->input('email');
        $user->role_id = $role->id;
        $user->password = bcrypt($request->input('password'));
        $user->save();
        
        
        //set Comelec entity
        $comelec = new Comelec();
        $comelec->first_name = ucwords($request->input('first_name'));
        $comelec->middle_name = ucwords($request->input('middle_name'));
        $comelec->last_name = ucwords($request->input('last_name'));
        
        $processedby = JWTAuth::toUser();
        $comelec->processed_by = $processedby->id;
        
        
        //save the comelec linked with the user account .
        
        $user->comelecProfile()->save($comelec);

        //
        return (new ComelecResource($comelec))->additional([
            'externalMessage' => "New Comelec has been created.",
            'internalMessage' => 'Comelec created.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comelec  $comelec
     * @return \Illuminate\Http\Response
     */
    public function show(Comelec $comelec)
    {
       
        return new ComelecResource($comelec);
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comelec  $comelec
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comelec $comelec)
    {
        $this->authorize('updateComelec',User::class);
        
        
        $request->validate([
            'email' => [
                'required',
                Rule::unique('users')->ignore($comelec->user->id)
            ],
            'status' => 'nullable|min:6|max:10',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|min:4|max:6'
        ]);
                
        
        $comelec->status = ucwords($request->input('status'));
        $comelec->birthdate = new Carbon($request->input('birthdate')); 
        $comelec->gender = ucwords($request->input('gender'));
        $user = User::findOrFail($comelec->user->id);
        $user->email = $request->input('email');
        
        $processor = JWTAuth::toUser();
        $comelec->processed_by = $processor->id;        
        $comelec->user()->associate($user);
        $comelec->save();

        return (new ComelecResource($comelec))->additional([
            'externalMesage' => "Comelec has been successfully updated.",
            'internalMessage' => "Comelec Updated."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comelec  $comelec
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comelec $comelec)
    {
        $this->authorize('deleteComelec',User::class);

        $processor = JWTAuth::toUser();

        $comelec->processed_by = $processor->id;

        $comelec->save();

        $account = User::find($comelec->user->id);
        $comelec->delete();

        $account->delete();

        

        return (new ComelecResource($comelec))->additional([
            'externalMessage' => "$comelec->first_name $comelec->last_name has been deleted.",
            'internalMessage' => 'Comelec Deleted.',
            
        ]);
    }

    //trashed index
    public function trashedIndex(Request $request){
        
        $items = $request->has('items') ? $request->items : $this->items ; 

        $orderBy = $request->has('orderBy') ? $request->orderBy : $this->orderBy ;

        $orderValue = $request->has('orderValue') ? $request->orderValue : $this->orderValue;

        return new ComelecCollection(Comelec::with(['user'=>function($q){
            $q->onlyTrashed();
        }])->onlyTrashed()->orderBy($orderBy,$orderValue)->paginate($items)->appends([
            'items' => $items,
            'orderBy' => $orderBy,
            'orderValue' => $orderValue
        ]));
            
    }

    //show trashed
    public function showTrashed($id){
        
        return new ComelecResource(Comelec::with(['user'=>function($q){
            $q->withTrashed();
        }])->onlyTrashed()->findorFail($id));
        
    }

    //restore
    public function restore(Request $request,$id){
        
        $this->authorize('restoreComelec',User::class);
    
        $restoreSubject = Comelec::onlyTrashed()->findOrFail($id);
        
        $restoreAccount = User::onlyTrashed()->findOrFail($restoreSubject->user_id);

        $processor = JWTAuth::toUser();
        
        $restoreSubject->restore();

        $restoreSubject->processed_by = $processor->id;

        $restoreSubject->save();
        
        $restoreAccount->restore();

        return (new ComelecResource($restoreSubject))->additional([
            'externalMessage' => "$restoreSubject->first_name $restoreSubject->last_name has been restored.",
            'internalMessage' => "Comelec restored.",
            
        ]);
    }


}


