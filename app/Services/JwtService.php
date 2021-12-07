<?php
namespace App\Services;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Illuminate\Support\Facades\Config;

class JwtService
{
    public static function jwtToken(){
        $key = Config::get('constant.Key');
            $payload = array(
                "iss" => "localhost",
                "aud" => time(),
                "iat" => 1356999524,
                "nbf" => 1357000000
            );

            $jwt = JWT::encode($payload, $key, 'HS256');
            return $jwt;
    }

    public static function encodeJson($jwt){
        $token = array("remember_token"=>$jwt);
        return json_encode($token);
    }
}
