<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Classes\Table;
use App\Enums\Setting;
use JWTAuth;
use Illuminate\Support\Facades\Crypt;

class Diagnosis extends Controller
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
        $data = table::diagnosis()
        ->join('patients', 'patients.id', '=', 'diagnosis.patients_id')
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
            'diagnosis' => 'required',
            'problem' => 'required',
            'patient' => 'required',
            'status' => 'required'
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $diagnosis= $request->diagnosis;
        $problem = $request->problem;
        $patient=$request->patient;
        $specialisation=$request->specialisation;
        $created_by=JWTAuth::parseToken()->authenticate()->id;
        $status = $request->status;
        table::diagnosis()->insert([
            [
                'diagnosis' => $diagnosis,
                'specialisation' => $specialisation,
                'problem' => $problem,
                'patients_id' => $patient,
                'created_by' => $created_by,
                'status' => $status
            ],
        ]);
        return $this->successResponse(200,trans("Success! diagnosis data is saved!"), "");
    }

    public function search(Request $request){
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
            $data = table::diagnosis()
            ->select('users.id', 'users.name', 'patients.*','diagnosis.*')
            ->join('patients', 'patients.id', '=', 'diagnosis.patients_id')
            ->join('users', 'users.id', '=', 'diagnosis.created_by')
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
        $diagnosis=table::diagnosis()->find($id);
        $data=[
            "e_id"=>$e_id,
            "diagnosis"=>$diagnosis
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
            'diagnosis' => 'required',
            'problem' => 'required',
            'patient' => 'required',
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $diagnosis= $request->diagnosis;
        $problem = $request->problem;
        $patient=$request->patient;
        $specialisation=$request->specialisation;
        // $created_by=JWTAuth::parseToken()->authenticate()->id;
        $status = $request->status;
        $e_id = ($id == null) ? 0 : Crypt::decryptString($id);
        table::diagnosis()->where('id', $e_id)->update(
            [
                'diagnosis' => $diagnosis,
                'specialisation' => $specialisation,
                'problem' => $problem,
                'patients_id' => $patient,
                'status' => $status
            ]
        );
        return $this->successResponse(200,trans("Success! diagnosis data is updated!"), "");
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
        table::diagnosis()->where('id', $e_id)->delete();
        return $this->successResponse(200,trans("Success! diagnosis data is deleted!"));
    }
}
