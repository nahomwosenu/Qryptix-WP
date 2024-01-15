<?php
require_once __DIR__.'/Persist.php';
class Settings
{
    var $id;
    var $clientId;
    var $k;
    var $v;

    public function __construct(){

    }
    static function save($setting){
        if(self::exists($setting->clientId,$setting->k)){
            return false;
        }
        Persist::save($setting,'settings');
        return true;
    }
    static function exists($clientId, $key){
        $q="select * from settings where clientId=? and k=?";
        return count(Persist::query($q,$clientId,$key))>0;
    }

    static function getValueByClient($clientId, $key){
        $q="select * from settings where clientId=? and k=?";
        $r=Persist::query($q,$clientId,$key);
        if(count($r)>0){
            return json_decode($r[0]['v'],1);
        }
        return null;
    }

    public static function update(Settings $s)
    {
        $q="update settings set v=? where clientId=? and k=?";
        return Persist::executeUpdate($q,$s->v,$s->clientId,$s->k);
    }

}