<?php
session_start();
// Require the Dropbox PHP SDK
require __DIR__.'/../vendor/autoload.php';

// Use the Dropbox namespace
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
class DropboxAuth
{

    static $authHelper;
    static $app;
    static $callbackUrl;

    static function init(){
        DropboxAuth::$app = new DropboxApp('3qaahqqkpviajr2', 'iot3kixjzyqyqgx');
        $dropbox = new Dropbox(DropboxAuth::$app);
        $root = "http://localhost"; //(!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        DropboxAuth::$authHelper = $dropbox->getAuthHelper();
        DropboxAuth::$callbackUrl=$root."/qryptix-backend/api/v1/dropbox/auth_callback.php";
    }

    static function oauth2($client_id){
        // Create an app object with your app key and app secret
        $_SESSION['clientId']=$client_id;
        self::init();
        try
        {
            $authUrl = DropboxAuth::$authHelper->getAuthUrl(self::$callbackUrl,[],$client_id);

            return $authUrl;
        }

        catch
        (\Kunnu\Dropbox\Exceptions\DropboxClientException $e) {
            return $e->getMessage();
        }

    }


}