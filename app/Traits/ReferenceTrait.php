<?php
namespace App\Traits;
use DB;
use App\Classes\Table;

trait ReferenceTrait {


    public static function generateReferenceNumber() {
        $reference = rand(10*45, 100*98)  . rand(10*45, 100*98). rand(10*45, 100*98);

        return $reference;
    }
  
}