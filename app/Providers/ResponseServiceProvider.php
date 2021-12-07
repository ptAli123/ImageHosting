<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success',function(){
            return response()->json([
                'success' => true,
                'message' => "Successfully Done"
            ],200);

        });

        Response::macro('error',function(){
            return response()->json([
                'success' => false,
                'message' => "wronge Credentials",
            ],400);

        });
    }
}
