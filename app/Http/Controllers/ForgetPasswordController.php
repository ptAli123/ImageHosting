<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Services\DatabaseConnectionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordController extends Controller
{
    public function forgetPasword(Request $request){
        $varify_token=rand(100,100000);
        $collection = new DatabaseConnectionService();
        $conn = $collection->getConnection('users');
        $conn->updateOne(array("email"=>$request->email),array('$set'=>array("forget_password_varify_token" => $varify_token)));
        $details = [
            'title' => 'Forget password Mail',
            'link' => $varify_token
        ];
        try{
            //$mail = new MailJob($request->email,$details);
            //dispatch($mail);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->json(['token' => $varify_token]);
        //return response()->json(['message' => 'Mail send...']);
    }

    public function updatePassword(ForgetPasswordRequest $request){
        $newPassword = hash::make($request->password);
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('users');
            $conn->updateOne(array("forget_password_varify_token"=>(int)$request->password_token),array('$set'=>array("password" => $newPassword)));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }
}
