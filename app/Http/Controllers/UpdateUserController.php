<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Services\DatabaseConnectionService;
use Exception;
use Illuminate\Http\Request;

class UpdateUserController extends Controller
{
    function userUpdate(UserUpdateRequest $request){
        $data_to_update = [];
        foreach ($request->all() as $key => $value) {
            if (in_array($key, ['name', 'email', 'age','password'])) {
                $data_to_update[$key]=$value;
            }
        }

        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('users');
            $conn->updateOne(array('remember_token'=>$request->remember_token),
                                array('$set'=>$data_to_update));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }
}
