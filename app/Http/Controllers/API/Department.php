<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Classes\Table;
use App\Enums\Setting;
use Illuminate\Support\Facades\Crypt;

class Department extends Controller
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
        $data = table::department()
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
            'department' => 'required|max:200',
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $department = $request->department;
        $description = $request->description;
        $status = 1;
        table::department()->insert([
            [
                'department' => $department,
                'description' => $description,
                'status' => $status
            ],
        ]);
        return $this->successResponse(200,trans("Success! department data is saved!"), "");
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
        $department=table::department()->find($id);
        $data=[
            "e_id"=>$e_id,
            "department"=>$department
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
            'department' => 'required|max:200',
        ]);
        if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
        $department = $request->department;
        $description = $request->description;
        $status = $request->status;
        $e_id = ($id == null) ? 0 : Crypt::decryptString($id);
        table::department()->where('id', $e_id)->update(
            [
                'department' => $department,
                'description' => $description,
                'status' => $status
            ]
        );
        return $this->successResponse(200,trans("Success! department data was updated!"), "");
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
        table::department()->where('id', $e_id)->delete();
        return $this->successResponse(200,trans("Success! department data is deleted!"));
    }
}
