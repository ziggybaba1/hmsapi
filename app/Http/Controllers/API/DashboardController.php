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

class DashboardController extends Controller
{
    public function index(){
        if (!$this->Logged())
        {
            return $this->failureResponse(401,trans("denied!")); 
        }
        $patient = table::patient()->latest()->take(10)->get();
        $diagnosis = table::diagnosis()
        ->select('users.id', 'users.name', 'patients.*','diagnosis.*')
        ->join('patients', 'patients.id', '=', 'diagnosis.patients_id')
        ->join('users', 'users.id', '=', 'diagnosis.created_by')
        
        ->take(10)
        ->get();
        $appointment = table::appointment()
        ->join('patients', 'patients.id', '=', 'appointments.patient_id')
        ->take(10)
        ->get();
        $count_patient=table::patient()->count();
        $count_diagnosis=table::diagnosis()->where('status',0)->count();
        $count_appointment=table::appointment()->where('status',0)->count();

        $data=[
            "patient"=>$patient,
            "diagnosis" => $diagnosis,
            "appointment"=>$appointment,
            "count_patient"=>$count_patient,
            "count_diagnosis"=>$count_diagnosis,
            "count_appointment"=>$count_appointment
        ];
            return $this->successResponse(200,'success',$data);
    }
}
