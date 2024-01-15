<?php

class Common
{
    static $SETTING_DROPBOX_AUTH="dropbox_auth";

    static $PLAN_BASIC_PRICE="5.99";
    static $PLAN_PREMIUM_PRICE="9.99";

    static function createUUID(){
        $a="abcdefghijklmnopqrstuvwxyz1234567890";
        $r="";
        for($i=0;$i<15;$i++){
            $index=rand(0,strlen($a));
            $r.=substr($a,$index,1);
        }
        return $r;
    }

    public static function getPriceForPlan($planType)
    {
        $planType=strtolower(trim($planType));
        if($planType==='basic')
            return self::$PLAN_BASIC_PRICE;
        if($planType==='premium')
            return self::$PLAN_PREMIUM_PRICE;
    }
}