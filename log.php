<?php
$requestFrom= $_SERVER['REMOTE_ADDR'] ?? null;
if($requestFrom===null){
    $requestFrom= $_SERVER['HTTP_REFERER'] ?? "UNKNOWN_SOURCE";
}
$request=json_encode($_REQUEST);
$inputData=file_get_contents('php://input') ?? "";

$logData="time: ".date('y-m-d h:i')."\r\nSource: ".$requestFrom."\r\nRequest: ".$request."\r\nInputData: ".$inputData."\r\n\r\n\r\n";
file_put_contents(__DIR__.'/log.txt',$logData,FILE_APPEND);