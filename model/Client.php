<?php
require_once __DIR__.'/Persist.php';
require_once __DIR__."/ClientPlan.php";
class Client
{
    var $id;
    var $instanceId;
    var $email;
    var $domain;
    var $emailVerified;
    var $apiKey;

    public function __construct(){

    }
    static function save($client){
        return Persist::save($client,'client');
    }
    static function exists($email){
        $q="select * from client where email = ?";
        return count(Persist::query($q,$email)) > 0;
    }
    static function getByEmail($email){
        $q="select * from client where email = ?";
        $rs=Persist::query($q,$email);
        if(count($rs)>0){
            $client=$rs[0];
            $client['plan']=ClientPlan::getByClient($client['id']);
            return $client;
        }
        return null;
    }
    static function getAll(){
        $q="select * from client";
        $rs=Persist::query($q);
        $all=array();
        for($i=0;$i<count($rs);$i++){
            $client=$rs[$i];
            $client['plan']=ClientPlan::getByClient($client['id']);
            $all[$i]=$client;
        }
        return $all;
    }

    public static function getByAPI($apiKey)
    {
        $q="select * from client where apiKey = ?";
        $rs=Persist::query($q,$apiKey);
        if(count($rs)>0){
            $client=$rs[0];
            $client['plan']=ClientPlan::getByClient($client['id']);
            return $client;
        }
        return null;
    }

}