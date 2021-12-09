<?php

namespace App\Http\Controllers;

use App\Service\JwtService as ServiceJwtService;
use App\Services\DatabaseConnectionService;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Services\JwtService;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
     /**
     * Take email and password
     * verify email and password
     * return jwt tokken
     */
    function login(Request $request){
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('users');
            $data = $conn->findOne(["email" => $request->email]);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        if (Hash::check($request->password,$data["password"])){
            $jwt = JwtService::jwtToken();
            $conn->updateOne(array("email"=>$request->email),
            array('$set'=>array("remember_token" => $jwt)));
            return JwtService::encodeJson($jwt);
        }
        else{
            return response()->json(["message" =>"Wronge Credential"]);
        }
    }

    /**
     * Take remember token
     * verify token and unset remember token
     * return success
     */
    function logout(Request $request){
        $collection = new DatabaseConnectionService();
        $conn = $collection->getConnection('users');
        $data = $conn->updateOne(array('remember_token'=>$request->remember_token),
        array('$unset'=>array('remember_token'=>'')));
        return response()->success();
    }
}
