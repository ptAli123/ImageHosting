<?php

use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MailVarificationController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\UpdateUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/sign-up',[SignUpController::class,'signUp'])->middleware("signUp");
Route::get('/mail-confirmation/{email}/{varify_token}',[MailVarificationController::class,'confirmed']);


Route::post('/login',[LoginController::class,'login']);
Route::post('/logout',[LoginController::class,'logout'])->middleware("userAuth");
Route::post('/user-update',[UpdateUserController::class,'userUpdate'])->middleware("userAuth");


Route::post('/forget-password',[ForgetPasswordController::class, 'forgetPasword'])->middleware("forgetPasswordAuth");
Route::post('/forget-password-update',[ForgetPasswordController::class, 'updatePassword']);
