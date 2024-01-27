<?php
header("content-type: application/json");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
require_once __DIR__."/../../../model/Client.php";
require_once __DIR__."/../../../model/ClientPlan.php";
require_once __DIR__."/../../../util/Common.php";
require_once __DIR__."/../../../log.php";

if(isset($_POST['register'])){
    $client = new Client();
    $client->email=$_POST['email'];
    $client->instanceId=$_POST['instanceId'];
    $client->domain=$_POST['domain'];
    $client->emailVerified=false;
    $client->apiKey=Common::createUUID();
    if(Client::exists($client->email)){
        $rs=array();
        $rs['status']='error';
        $rs['message']='This email address is already taken, if you forget your password you can reset it from your Qryptix dashboard';
        die(json_encode($rs));
    }
    $id=Client::save($client);
    $client->id=$id;
    $planType=$_POST['plan'];
    $plan=array();
    $plan['type']=$planType;
    $plan['price']=Common::getPriceForPlan($planType);
    $cp=new ClientPlan();
    $cp->clientId=$client->id;
    $cp->plan=json_encode($plan);
    ClientPlan::save($cp);
    $rs['status']='success';
    $rs['client']=$client;
    $rs['plan']=$cp;
    die(json_encode($rs));
}
if(isset($_GET['verify'])){
    $d=file_get_contents("php://input");
    $data=json_decode($d,1);

    if(isset($data['apiKey']) && isset($data['email'])){
        $client=Client::getByAPI($data['apiKey']);
        if($client!=null){
            $r=array();
            $r['status']='success';
            $r['message']='Activation success';
            die(json_encode($r));
        }else{
            $r=array();
            $r['status']='error';
            $r['message']='Error 502';
            die(json_encode($r));
        }
    }else{
        $r=array();
        $r['status']='error';
        $r['message']='error 500';
        die(json_encode($r));
    }
}
if(isset($_GET['payment'])){
    $data=file_get_contents('php://input');
    $json=json_decode($data,1);
    if(!isset($json['paymentMethod']) || !isset($json['subscriptionType']) || !isset($json['txReference'])){
        $r=array();
        $r['status']='error';
        $r['message']='Missing parameters in this request';
        die(json_encode($r));
    }
    if(verifyTx($json['txReference'])){
    $payment=array();
    $payment['type']=$json['subscriptionType'] === 'monthly' ? 'monthly':'yearly';
    $payment['paymentMethod']=$json['paymentMethod'];
    $payment['timestamp']=time();
    $payment['success']=true;
    $payment['reference']=$json['txReference'];
    $r=array();
    $r['status']='success';
    $r['message']='We have received your payment';
    die(json_encode($r));
    }else{
        $r['status']='error';
        $r['message']="We couldn't verify your payment";
        die(json_encode($r));
    }
}
function verifyTx($tx):bool{
    return true;
}
$r=array();
$r['status']='error';
$r['message']='Something went wrong';
die(json_encode($r));