<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Mail\VarificationMail;
use App\Services\DatabaseConnectionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    /**
     * Take email
     * send otp on mail
     * set otp in database
     * return send mail
     */
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
            Mail::to($request->email)->send(new VarificationMail($details));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        //return response()->json(['token' => $varify_token]);
        return response()->json(['message' => 'Mail send...']);
    }

    /**
     * Take otp and new password
     * verify otp and set new password
     * return success
     */

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
