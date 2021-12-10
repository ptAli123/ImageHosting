<?php

namespace App\Http\Middleware;

use App\Services\DatabaseConnectionService;
use Closure;
use Exception;
use Illuminate\Http\Request;

class PrivateAccessMiddleware
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
        $filename = explode('/',$request->filename);
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $data = $conn->findOne(array("photo" => $request->filename));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        if ($data && $data['private'] == 1) {
            $data1 = $conn->findOne(["_id" => $data['_id'],"shared.mail" => $request->email]);
            if ($data1) {
                return $next($request->merge(["data" => $data,"photoName" => $filename[5]]));
            } else {
                return response()->json(['message' => "you are not allowed"]);
            }
        } else {
            return response()->json(['message' => "you are not allowed"]);
        }
    }
}
