<?php
/*
* Workday - A time clock application for employees
* Support: official.codefactor@gmail.com
* Version: 1.6
* Author: Brian Luna
* Copyright 2020 Codefactor
*/
namespace App\Classes;

use DB;

Class table {

    public static function patient() 
    {
      $patient = DB::table('patients');
      return $patient;
    }

    public static function department() 
    {
      $department = DB::table('departments');
      return $department;
    }

    public static function appointment() 
    {
      $appointment = DB::table('appointments');
      return $appointment;
    }

    public static function diagnosis() 
    {
      $diagnosis = DB::table('diagnosis');
      return $diagnosis;
    }

    public static function users() 
    {
      $users = DB::table('users');
      return $users;
    }

}