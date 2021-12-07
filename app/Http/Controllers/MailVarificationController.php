<?php

namespace App\Http\Controllers;

use App\Services\DatabaseConnectionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MailVarificationController extends Controller
{
    function confirmed($email,$varify_token){
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('users');
            $conn->updateOne(array("email"=>$email,"verify_token"=>(int)$varify_token),
                  array('$set'=>array("email_verified" => 1)));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }
}
