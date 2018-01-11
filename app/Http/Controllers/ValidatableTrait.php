<?php

 namespace App\Http\Controllers;

 use Illuminate\Support\Facades\Validator;

 trait ValidatableTrait
 {
     public $errors;

     public function validate($data, $rules) {
         $validator = Validator::make($data, $rules);

         if ($validator->fails()) {
             $this->errors = $validator->errors()->toArray();
             return false;
         }

         return true;
     }

     public function validationErrorResponse()
     {
         if (!$this->errors) {
             return null;
         }

         return response()->json([
             'errors' => $this->errors
         ], 422);
     }
 }