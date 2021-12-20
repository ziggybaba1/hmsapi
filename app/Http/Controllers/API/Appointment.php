<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Classes\Table;
use App\Enums\Role;
use App\Enums\Setting;
use JWTAuth;
use Illuminate\Support\Facades\Crypt;

class Appointment extends Controller
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
        $data = table::appointment()
        ->select('users.id', 'users.name', 'patients.*','appointments.*')
        ->join('patients', 'patients.id', '=', 'appointments.patient_id')
        ->join('users', 'users.id', '=', 'appointments.doctor_id')
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
            'appointdate' => 'required|date',
            'department' => 'required',
            'doctor' => 'required',
            'patient' => 'required',
            'diagnosis' => 'required'
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $appoint= date('Y-m-d H:i:s', strtotime($request->appointdate));;
        $problem = $request->problem;
        $department=$request->department;
        $doctor=$request->doctor;
        $diagnosis=$request->diagnosis;
        $patient=$request->patient;
        $created_by=JWTAuth::parseToken()->authenticate()->id;
        $status = 0;
        $serialno=$this->generateReferenceNumber();
        table::appointment()->insert([
            [
                'appointdate' => $appoint,
                'problem' => $problem,
                'serialno' => $serialno,
                'patient_id' => $patient,
                'department_id' =>$department,
                'doctor_id' => $doctor,
                'diagnosis_id' => $diagnosis,
                'created_by' => $created_by,
                'status' => $status
            ],
        ]);
        return $this->successResponse(200,trans("Success! appointment data is saved!"), "");
    }



    public function search(Request $request){
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
            $data = table::appointment()
            ->select('users.id', 'users.name', 'patients.*','appointments.*','departments.*')
            ->join('patients', 'patients.id', '=', 'appointments.patient_id')
            ->join('users', 'users.id', '=', 'appointments.doctor_id')
            ->join('departments', 'departments.id', '=', 'appointments.department_id')
            ->orWhere('uuid',$request->idno)
            ->orWhere('name',$request->doctor)
            ->get();
           
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
        // $appointment=table::appointment()
        // ->find($id);
        $doctors=table::users()
        ->where('role',Role::Doctor)
        ->select('users.id', 'users.name')
        ->get();
        $diagnosis=table::diagnosis()
        ->where('patients_id',$id)
        ->where('status',1)
        ->get();
        $department=table::department()
        ->get();

        $data=[
            "e_id"=>$e_id,
            // "appointment"=>$appointment,
            "doctor"=>$doctors,
            "diagnosis"=>$diagnosis,
            'department'=>$department
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
            'appointdate' => 'required|date',
            'department' => 'required',
            'doctor' => 'required',
            'patient' => 'required',
            'diagnosis' => 'required'
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $appoint= date('Y-m-d H:i:s', strtotime($request->appointdate));;
        $problem = $request->problem;
        $department=$request->department;
        $doctor=$request->doctor;
        $diagnosis=$request->diagnosis;
        $patient=$request->patient;
        $status = 0;
        $e_id = ($id == null) ? 0 : Crypt::decryptString($id);
        table::appointment()->where('id', $e_id)->update(
            [
                'appointdate' => $appoint,
                'problem' => $problem,
                'patient_id' => $patient,
                'department_id' =>$department,
                'doctor_id' => $doctor,
                'diagnosis_id' => $diagnosis,
                'status' => $status
            ]
        );
        return $this->successResponse(200,trans("Success! appointment data is updated!"), "");
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
        table::appointment()->where('id', $e_id)->delete();
        return $this->successResponse(200,trans("Success! appointment data is deleted!"));
    }
}
