<?php

namespace App\Http\Middleware;

use App\Services\DatabaseConnectionService;
use Closure;
use Exception;
use Illuminate\Http\Request;

class UserAuthMiddleware
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
            $data = $conn->findOne(["remember_token"=>$request->remember_token]);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()]);
        }
        if ($data){
            return $next($request->merge(["data" => $data]));
        }
        else{
            return response()->json(['message' => 'you are not Allowed']);
        }
    }
}
