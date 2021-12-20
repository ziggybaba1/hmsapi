<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Classes\Table;
use App\Enums\Setting;
use App\Enums\Role;
use Illuminate\Support\Facades\Crypt;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
            $data = table::users()
            ->select('users.id as idd', 'users.name','users.role','users.email','users.department_id','departments.*')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->where('role',Role::Doctor)
            ->latest()
            ->paginate(Setting::paginate);
            return $this->successResponse(200,'success',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
        $validator= Validator::make($request->all(), [
            'name' => 'required|max:200',
            'email' => 'required|max:200',
            'password' => 'required|max:100',
            'department_id' => 'required',
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $name=$request->name;
        $email=$request->email;
        $password=$request->password;
        $department_id=$request->department_id;
        

        table::users()->insert([
            [
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'department_id'=> $department_id,
                'role' => Role::Doctor,
            ],
        ]);
        return $this->successResponse(200,trans("Success! doctor data is saved!"), "");
    }

    public function search(Request $request){
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
            $data = table::users()
            ->select('users.id as idd', 'users.name','users.role','users.email','users.department_id','departments.*')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->orWhere('name','LIKE', '%'.$request->idno.'%')
		->orWhere('department','LIKE', '%'.$request->idno.'%')
            ->paginate(Setting::paginate);
           
            return $this->successResponse(200,'success',$data);
    }

   public function searched($x, $y) {
        $data=[];
        if($y){
            foreach($x as $val){
                if (in_array($val->department, $y))
                array_push($data,$x);
              }
              return $data;
        }
        else{
            return [];
        }
       
    }

    public function retrieve(Request $request){
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
        $diagnosis=table::diagnosis()->find($request->data);
        $convdiag=$diagnosis->specialisation!==""?json_decode($diagnosis->specialisation, TRUE):null;
        $doctor = table::users()
        ->select('users.id as idd', 'users.name','users.role','users.email','users.department_id','departments.*')
        ->join('departments', 'departments.id', '=', 'users.department_id')
        ->where('role',Role::Doctor)
        ->get();
       $finalresult = $this->searched($doctor,$convdiag);
        return $this->successResponse(200,'success',$finalresult);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
        $e_id = ($id == null) ? 0 : Crypt::encryptString($id);
        $doctor=table::users()
        ->select('users.id', 'users.name','users.email','users.department_id')
        ->where('id',$id)
        ->first();
        $data=[
            "e_id"=>$e_id,
            "detail"=>$doctor
        ];
        return $this->successResponse(200,'success',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
        $validator= Validator::make($request->all(), [
            'name' => 'required|max:200',
            'department_id' => 'required',
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $name=$request->name;
        $email=$request->email;
        $password=$request->password;
        $department_id=$request->department_id;
        $e_id = ($id == null) ? 0 : Crypt::decryptString($id);        
        $oldpassword=table::users()->where('id', $e_id)->first()->password;
        table::users()->where('id', $e_id)->update(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password?bcrypt($password):$oldpassword,
                'department_id'=> $department_id,
            ]
        );
        return $this->successResponse(200,trans("Success! doctor data is updated!"), "");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
        $e_id = ($id == null) ? 0 : Crypt::decryptString($id);
        table::users()->where('id', $e_id)->delete();
        return $this->successResponse(200,trans("Success! doctor data is deleted!"));
    }
}
