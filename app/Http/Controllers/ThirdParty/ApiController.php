<?php

namespace App\Http\Controllers\ThirdParty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Classes\TokenGenerator;
use App\Classes\DiagnosisClient;

class ApiController extends Controller
{

    public function Authenticate(){
       
    }

    public function symptoms(){
       $tokenize=new TokenGenerator(env('MEDIC_USERNAME'),env('MEDIC_PASSWORD'),env('MEDIC_AUTHURL')."login");
       $token =$tokenize->loadToken();
      $symptomData=new DiagnosisClient($token, env('MEDIC_HEALTHURL'), 'en-gb');
      $ymptoms=$symptomData->loadSymptoms();
    return $this->successResponse(200,'success',$ymptoms);
        // env('DB_CONNECTION', 'mysql')
    }

    public function issues(){
    //    
        // env('DB_CONNECTION', 'mysql')
    }

    public function diagnosis(Request $request){
        $tokenize=new TokenGenerator(env('MEDIC_USERNAME'),env('MEDIC_PASSWORD'),env('MEDIC_AUTHURL')."login");
       $token =$tokenize->loadToken();
      $diagnosisData=new DiagnosisClient($token, env('MEDIC_HEALTHURL'), 'en-gb');
      $diagnosis=$diagnosisData->loadDiagnosis(json_decode($request->symptom),$request->gender,$request->dob);
    return $this->successResponse(200,'success',$diagnosis);
        // env('DB_CONNECTION', 'mysql')
    }
}
