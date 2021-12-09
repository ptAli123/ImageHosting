<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\SignUpRequest;
use App\Jobs\MailJob;
use App\Mail\VarificationMail;
use App\Services\DatabaseConnectionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SignUpController extends Controller
{
    /**
     * Take sign up Credentials
     * save in database and send confirmation mail with otp
     * return send mail message
     */
    function signUp(SignUpRequest $request) {
        $varify_token=rand(100,100000);
        $collection = new DatabaseConnectionService();
        $conn = $collection->getConnection('users');
        $path = $request->file('profileImage')->store('profileImage');
        $document = array(
            "name" => $request->name,
            "email" => $request->email,
            "profileImage" => $path,
            "password"=> hash::make($request->password),
            "age"=>$request->age,
            "mail_verify_token" => $varify_token,
            "status" => 1
            );
        $conn->insertOne($document);
        $details = [
            'title' => 'confirmation Mail',
            'link' => 'http://127.0.0.1:8000/api/mail-confirmation/'.$request->email.'/'.$varify_token
        ];
        try{
            //$mail = new MailJob($request->email,$details);
            //dispatch($mail);
            Mail::to($request->email)->send(new VarificationMail($details));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->json(["message"=>"mail send...."]);
        //return response()->json(['link' => 'http://127.0.0.1:8000/api/mail-confirmation/'.$request->email.'/'.$varify_token]);
    }
}
