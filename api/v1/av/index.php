<?php
header("Content-Type: application/json");
require_once __DIR__."/../../../util/VulScanner.php";
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
