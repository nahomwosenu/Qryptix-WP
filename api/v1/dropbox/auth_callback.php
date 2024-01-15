<?php
require_once __DIR__."/../../../util/DropboxAuth.php";
require_once __DIR__."/../../../model/Settings.php";
require_once __DIR__."/../../../model/Client.php";
require_once __DIR__."/../../../util/Common.php";
require_once __DIR__."/../../../model/URLMap.php";

DropboxAuth::init();

if(isset($_GET['code']) && isset($_GET['state'])){
    $code=$_GET['code'];
    $state=$_GET['state'];
    $accessToken = DropboxAuth::$authHelper->getAccessToken($code, $state, DropboxAuth::$callbackUrl);
    $token=$accessToken->getToken();
    if($token!=null){
        $clientId=$_SESSION['clientId'];
        $s=new Settings();
        $s->clientId=$clientId;
        $s->k=Common::$SETTING_DROPBOX_AUTH;
        $r=array();
        $r['accessToken']=$token;
        $s->v=json_encode($r);
        if(!Settings::save($s)){
            Settings::update($s);
        }
        // setup complete, redirect user to his website admin page
        $url=URLMap::getByClient($s->clientId);
            echo "<h1>Dropbox backup setup complete, you can close this window now</h1>";
            echo "<script>window.opener.location.reload();</script>";
    }else{

    }
}
