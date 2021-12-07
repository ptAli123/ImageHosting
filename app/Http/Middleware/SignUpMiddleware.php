<?php

namespace App\Http\Middleware;

use App\Services\DatabaseConnectionService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignUpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('users');
            $data = $conn->findOne(["email"=>$request->email]);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()]);
        }
        if ($data){
            return response()->json(["message" => "Wronge Credentials"], 404);
        }
        else{
            return $next($request);
        }
    }
}
