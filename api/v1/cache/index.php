<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Authorization");
require_once __DIR__."/../../../model/Client.php";
require_once __DIR__."/../../../model/Cache.php";
$headers=getallheaders();
if(!isset($headers['Authorization'])){
    $r=array();
    $r['status']='error';
    $r['message']='Missing api key';
    die(json_encode($r));
}
$apiKey=explode(' ',$headers['Authorization']);

if(isset($_GET['get']) && isset($_GET['key'])){
    $client=Client::getByAPI($apiKey);
    if($client===null){
        $r=array();
        $r['status']='error';
        $r['message']='Invalid or missing api key';
        die(json_encode($r));
    }else{
        $id=$client['id'];
        $cache=Cache::getOne($id,$_GET['key']);
        if($cache===null){
            $r=array();
            $r['status']='error';
            $r['message']='Cache not found!';
            die(json_encode($r));
        }else{
            Cache::delete($cache);
            $r=array();
            $r['status']='success';
            $r['data']=$cache['data'];
            die(json_encode($r));
        }
    }
}