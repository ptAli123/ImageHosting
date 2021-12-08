<?php

use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MailVarificationController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\UpdateUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/upload-photo',[PhotosController::class,'uploadPhoto'])->middleware("userAuth");
Route::post('/remove-photo',[PhotosController::class,'removePhoto'])->middleware("userAuth");

Route::post('/list-photo',[PhotosController::class,'listPhoto'])->middleware("userAuth");
Route::post('/search-photo',[PhotosController::class,'searchPhoto'])->middleware("userAuth");
Route::post('/generate-link',[PhotosController::class,'generateLink'])->middleware("userAuth");


Route::any('/storage/photos/{filename}',[PhotosController::class,'accessPhoto']);


Route::post('/make-photo-public',[PhotosController::class,'makePhotoPublic'])->middleware("userAuth");
Route::post('/make-photo-private',[PhotosController::class,'makePhotoPrivate'])->middleware("userAuth")->middleware("forgetPasswordAuth");
Route::post('/make-photo-hidden',[PhotosController::class,'makePhotoHidden'])->middleware("userAuth");
Route::post('/remove-specific-private-Mail',[PhotosController::class,'removeSpecificPrivateMail'])->middleware("userAuth");
