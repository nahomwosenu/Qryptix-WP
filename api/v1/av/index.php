<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Authorization");
require_once __DIR__."/../../../util/VulScanner.php";
require_once __DIR__."/../../../model/Client.php";
require_once __DIR__."/../../../log.php";
require_once __DIR__."/../../../model/Cache.php";
$headers=getallheaders();
if(!isset($headers['Authorization'])){
    $r=array();
    $r['status']='error';
    $r['message']='Missing api key';
    die(json_encode($r));
}
$apiKey=trim(explode(' ',$headers['Authorization']));
$client=Client::getByAPI($apiKey);
if(isset($_GET['scan']) && isset($_GET['domain'])){
    $domain=$_GET['domain'];
    $scan=new VulScanner($domain, $client['id']);
    $scan->startScan();
    $r=array();
    $r['status']='success';
    $r['message']='Job scheduled successfully';
    die(json_encode($r));
}

if(isset($_GET['get_result'])){
    $cache=Cache::getOne($client['id'],'av_result');
    if($cache!==null){
        $r=array();
        $r['status']='success';
        $data=json_decode($cache['data'],1);
        $r['data']=$data['data'];
        die(json_encode($r));
    }
    else{
        $r=array();
        $r['status']='error';
        $r['message']='Cache miss or job might still be in queue';
        die(json_encode($r));
    }
}
