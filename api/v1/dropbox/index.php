<?php
header("Content-Type: application/json");
require_once __DIR__."/../../../model/Client.php";
require_once __DIR__."/../../../model/Settings.php";
require_once __DIR__."/../../../model/URLMap.php";
require_once __DIR__."/../../../util/Common.php";
require_once __DIR__."/../../../util/DropboxAuth.php";
require_once __DIR__."/../../../log.php";

if(isset($_GET['hasAuth'])){

}
if(isset($_GET['newAuth'])){
    $apiKey=$_GET['apiKey'];
    $client=Client::getByAPI($apiKey);
    if($client==null){
        $rs=array();
        $rs['status']='error';
        $rs['message']='Client not available';
        die(json_encode($rs));
    }
    $val=Settings::getValueByClient($client['id'],Common::$SETTING_DROPBOX_AUTH);
    if($val==null){
        //proceed with new dropbox auth
        $map=new URLMap();
        $map->clientId=$client['id'];
        $map->url=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']:'localhost';
        URLMap::save($map);
        $url=DropboxAuth::oauth2($client['id']);
        $rs=array();
        $rs['status']='success';
        $rs['url']=$url;
        //die(json_encode($rs));
        header("Location: $url");
    }else{
        //auth setup exists, return true
        $rs=array();
        $rs['status']='success';
        $rs['auth']=true;
        die(json_encode($rs));
    }
}