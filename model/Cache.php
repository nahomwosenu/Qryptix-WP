<?php

require_once __DIR__."/Persist.php";
class Cache
{
    var $id;
    var $clientId;
    var $cacheKey;
    var $data;

    function __construct()
    {
    }
    static function save($cache){
        Persist::save($cache,'cache');
    }
    static function getOne($clientId, $cacheKey){
        $one=Persist::query("select * from cache where clientId=? and cacheKey=?",$clientId,$cacheKey);
        return count($one) > 0 ? $one[0]:null;
    }

    public static function delete($id)
    {
        $query="delete from cache where id=$id";
        Persist::executeUpdate($query);
    }
}