<?php
require_once __DIR__."/Persist.php";
class URLMap
{
    var $id;
    var $clientId;
    var $url;

    static function save($map){
        Persist::save($map, 'url_map');
    }
    static function getByClient($clientId){
        $r=Persist::query("select * from url_map where clientId=?",$clientId);
        if(count($r)>0)
            return $r[0]['url'];
        return null;
    }
}