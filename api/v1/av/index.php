<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Authorization");
require_once __DIR__."/../../../util/VulScanner.php";
require_once __DIR__."/../../../log.php";
if(isset($_GET['scan']) && isset($_GET['domain'])){
    $domain=$_GET['domain'];
    $scan=new VulScanner($domain);
    $scan->startScan();
    $result=$scan->getResult();
    $r=array();
    $r['status']='success';
    $r['data']=$result;
    die(json_encode($r));
}
