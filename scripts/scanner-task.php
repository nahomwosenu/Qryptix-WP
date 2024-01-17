#!/usr/bin/php
<?php
require_once __DIR__."/../model/Cache.php";
require_once __DIR__."/../util/VulScanner.php";
include_once __DIR__."/../log.php";
$CACHE_KEY="av_result";
unset($argv[0]);
parse_str(implode('&',$argv), $_REQUEST);

$clientId=$_REQUEST['id'];
$domain=$_REQUEST['domain'];

$cmd="nmap -sV --script=vulscan/vulscan.nse $domain";
$result= shell_exec($cmd);
$l="CMD: $cmd \r\n client & domain: $clientId,$domain \r\n Result: ".$result;
d($l);
$scanner=new VulScanner($domain,$clientId);
$scanner->rawResult=$result;
$scanner->parse();
$data=$scanner->getResult();

if($data!=null){
    $json=array();
    $json['data']=$data;
    $cache=new Cache();
    $cache->clientId=$clientId;
    $cache->cacheKey=$CACHE_KEY;
    $cache->data=json_encode($json);
    Cache::save($cache);
}




