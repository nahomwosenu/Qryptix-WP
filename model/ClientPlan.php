<?php
require_once __DIR__.'/Persist.php';
class ClientPlan
{
    var $id;
    var $clientId;
    var $plan;
    var $payment;


    /***
     * plan example: { type=basic/premium/, price: price_USD }
     */
    /*
     Payment example:
    {
     "type": "monthly/yearly",
     "paymentMethod": "card|ewallet-name"
     "timestamp":"timestamp",
     "success": true/false,
    "reference":"tx reference"
    }
     */

    public function __construct(){

    }
    static function save($plan){
        Persist::save($plan, 'client_plan');
    }
    static function getByClient($clientId){
        return Persist::query("select * from client_plan where clientId=?",$clientId);
    }
    static function getAll(){
        return Persist::query("select * from client_plan");
    }


}