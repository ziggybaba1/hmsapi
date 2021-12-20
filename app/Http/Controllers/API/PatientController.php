<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Classes\Table;
use App\Enums\Setting;
use Illuminate\Support\Facades\Crypt;


class PatientController extends Controller
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
            $data = table::patient()
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
            'uuid' => 'required|max:200',
            'firstname' => 'required|max:200',
            'lastname' => 'required|max:100',
            'phone' => 'required|max:11|min:11',
            'sex' => 'required|max:11',
            'dob' => 'required|date|max:30',
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $reference = $request->uuid;
        $dob = date('Y-m-d', strtotime($request->dob));
        $firstname=$request->firstname;
        $middlename=$request->middlename;
        $lastname=$request->lastname;
        $phone=$request->phone;
        $email=$request->email;
        $sex=$request->sex;

        table::patient()->insert([
            [
                'uuid' => $reference,
                'dob' => $dob,
                'firstname' => $firstname,
                'email'=> $email,
                'lastname' => $lastname,
                'middlename' => $middlename,
                'phone' => $phone,
                'sex' => $sex,
            ],
        ]);
        return $this->successResponse(200,trans("Success! patient data is saved!"), "");
    }

    public function search(Request $request){
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
            $data = table::patient()
            ->orWhere('uuid','LIKE', '%'.$request->idno.'%')
		->orWhere('firstname','LIKE', '%'.$request->idno.'%')
		->orWhere('lastname','LIKE', '%'.$request->idno.'%')
            ->paginate(Setting::paginate);
           
            return $this->successResponse(200,'success',$data);
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
        $patient=table::patient()->find($id);
        $data=[
            "e_id"=>$e_id,
            "detail"=>$patient
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
            'firstname' => 'required|max:200',
            'lastname' => 'required|max:100',
            'phone' => 'required|max:11',
            'sex' => 'required|max:11',
            'dob' => 'required|date|max:30',
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $reference = $request->uuid;
        $dob = date('Y-m-d', strtotime($request->dob));
        $firstname=$request->firstname;
        $lastname=$request->lastname;
        $phone=$request->phone;
        $email=$request->email;
        $middlename=$request->middlename;
        $sex=$request->sex;
        $bloodgroup=$request->bloodgroup;
        $address=$request->address;
        $status=$request->status;
        
        $file = $request->file('image');

		if($file != null) 
		{
			$name = $request->file('image')->getClientOriginalName();
			$destinationPath = public_path() . '/assets/faces/';
			$file->move($destinationPath, $name);
		} else {
			$name = '';
		}
        
        $e_id = ($id == null) ? 0 : Crypt::decryptString($id);
        table::patient()->where('id', $e_id)->update(
            [
                'uuid' => $reference,
                'dob' => $dob,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'middlename' => $middlename,
                'address' =>$address, 
                'phone' => $phone,
                'sex' => $sex,
                'bloodgroup' => $bloodgroup,
                'image'=>$name,
                'status' =>$status
            ]
        );
        return $this->successResponse(200,trans("Success! patient data is updated!"));
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
        table::patient()->where('id', $e_id)->delete();
        return $this->successResponse(200,trans("Success! patient data is deleted!"));
    }
}
