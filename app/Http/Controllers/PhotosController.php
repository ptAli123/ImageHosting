<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoRequest;
use App\Services\DatabaseConnectionService;
use Exception;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
    /**
     * Take sign up Credentials
     * Save photo data in database
     * return success
     */
    function uploadPhoto(PhotoRequest $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $photo = $request->file('photo');
            $array = (array)$photo;
            $photoName = $array["\x00Symfony\Component\HttpFoundation\File\UploadedFile\x00originalName"];
            $photoData = explode('.',$photoName);
            $photoPath = $request->file('photo')->store('photos');
            $path=$_SERVER['HTTP_HOST']."/photo/storage/".$photoPath;
            $document = array(
                "user_id" => $request->data->_id,
                "date" => date("Y-m-d"),
                "time" => date("h:i:sa"),
                "name" => $photoData[0],
                "extensions" => $photoData[1],
                "hidden" => 1,
                "photo" => $path,
                );
            $conn->insertOne($document);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }

   /**
     * Take photo link
     * Check photo is public or not
     * return show Photo
     */
    function accessPhoto(Request $request, $filename) {
        $path1 = $_SERVER['HTTP_HOST']."/photo/storage/photos/".$filename;
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $data = $conn->findOne(array("photo" => $path1));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        if ($data && $data['public'] == 1) {
            $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
            $path = storage_path("app/photos".'/'.$filename);
            if (file_exists($path)) {
                return response()->download($path, null, $headers, null);
            }
        } else {
            return response()->json(['message' => "you are not allowed"]);
        }
    }

    /**
     * Return true if given mail exist in embbeded mail otherwise false
     */
    function checkMail($email,$data,$conn) {
        $data1 = $conn->findOne(["_id" => $data['_id'],"shared.mail" => $email]);
        if ($data1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Take photo link and mail
     * Check photo is public or not
     * return show Photo
     */
    function accessPhotoLogin(Request $request) {
        $filename = explode('/',$request->filename);
        $path1 = $_SERVER['HTTP_HOST']."/photo/storage/photos/".$filename[4];
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $data = $conn->findOne(array("photo" => $path1));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        if ($data && $data['private'] == 1) {
            if ($this->checkMail($request->email,$data,$conn)) {
                $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
                $path = storage_path("app/photos".'/'.$filename[4]);
                if (file_exists($path)) {
                    return response()->download($path, null, $headers, null);
                }
            }
        } else {
            return response()->json(['message' => "you are not allowed"]);
        }
    }

    /**
     * Return Photo if its hidden
     */
    function accessPhotoHidden(Request $request) {
        $filename = explode('/',$request->filename);
        $path1 = $_SERVER['HTTP_HOST']."/photo/storage/photos/".$filename[4];
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $data = $conn->findOne(array("photo" => $request->filename));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        if ($data && $data['hidden'] == 1) {
            $headers = ["Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0"];
            $path = storage_path("app/photos".'/'.$filename[4]);
            if (file_exists($path)) {
                return response()->download($path, null, $headers, null);
            }
        }
    }

    /**
     * take
     */
    function generateLink(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $id = new \MongoDB\BSON\ObjectId($request->photo_id);
            $data = $conn->findOne(array('_id' => $id,"user_id" => $request->data->_id));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->json(["Link" => $data['photo']]);
    }

    function removePhoto(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $id = new \MongoDB\BSON\ObjectId($request->photo_id);
            $conn->deleteOne(array('_id' => $id,"user_id" => $request->data->_id));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }

    function listPhoto(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $photos = $conn->find(['user_id' => $request->data->_id]);
            $photosArr = json_decode(json_encode($photos->toArray(),true));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->json($photosArr);
    }

    function searchPhoto(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $searchPera = [];
            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['date','time','name', 'extensions', 'hidden','private','public'])) {
                    $searchPera[$key]=$value;
                }
            }
            $photos = $conn->find($searchPera);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        $photosArr = json_decode(json_encode($photos->toArray(),true));
        return response()->json($photosArr);
    }

    function removeMails($id) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $conn->updateOne(array('_id'=>$id),array('$unset'=>array('shared'=>'')));
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
    }

    function makePhotoHidden(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $id = new \MongoDB\BSON\ObjectId($request->photo_id);
            $conn->updateOne(array("user_id"=>$request->data->_id,"_id" => $id),array('$set'=>array("public" => 0,"hidden"=>1,"private"=>0)));
            $this->removeMails($id);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }

    function makePhotoPublic(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $id = new \MongoDB\BSON\ObjectId($request->photo_id);
            $conn->updateOne(array("user_id"=>$request->data->_id,"_id" => $id),array('$set'=>array("public" => 1,"hidden"=>0,"private"=>0)));
            $this->removeMails($id);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }

    function makePhotoPrivate(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $id = new \MongoDB\BSON\ObjectId($request->photo_id);
            $conn->updateOne(array("user_id"=>$request->data->_id,"_id" => $id),array('$set'=>array("public" => 0,"hidden"=>0,"private"=>1)));
            $conn->updateOne(["user_id" => $request->data->_id,"_id" => $id], ['$push'=>["shared"=>["mail"=>$request->email]]]);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }

    function removeSpecificPrivateMail(Request $request) {
        try{
            $collection = new DatabaseConnectionService();
            $conn = $collection->getConnection('photos');
            $id = new \MongoDB\BSON\ObjectId($request->photo_id);
            $conn->updateOne(["user_id"=>$request->data->_id,"_id" => $id, "shared.mail"=>$request->email,"private" => 1], ['$pull'=>["shared"=>["mail"=>$request->email]]]);
        }catch(Exception $ex){
            return response()->json(['message' => $ex->getMessage()],422);
        }
        return response()->success();
    }

}
