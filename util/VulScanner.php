<?php
require_once __DIR__."/Common.php";
class VulScanner
{
    var $rawResult;
    var $result;
    var $url;
    var $clientId;

    function __construct($url,$clientId){
        $this->url=$url;
        $this->clientId=$clientId;
    }
    function startScan(){
        //$command="nmap -sV --script=vulscan/vulscan.nse $this->url";
        $command="/usr/bin/php ".__DIR__."../scripts/scanner-task.php id=$this->clientId domain=$this->url &";
        $this->rawResult=Common::executeCommand($command);
        //$this->parse();
    }

    public function parse()
    {
        $collect=array();
        $lines=explode(PHP_EOL,$this->rawResult);
        //echo "LINES COUNT: ".count($lines);
        if(count($lines)==0){
            $this->result=array();
            return;
        }
        for($i=0;$i<count($lines) && $i<1000;$i++){
            if(strstr($lines[$i],'[CVE-')){
                //echo "PROCESSING LINE: ".$lines[$i];
                $collect[]=$this->parseLine($lines[$i]);
            }
        }
        usort($collect,function($a,$b){
            return $a['score']<$b['score'];
        });
        if(count($collect)>10){
            $this->result=array_slice($collect,0,10);
        }
        else
        $this->result=$collect;
    }

    private function parseLine(string $line)
    {
        $key=strpos($line,'[');
        $start=strpos($line,']');
        if($start===false)
            return [];
        $description=substr($line,$start+1);
        $cve=substr($line, $key+1,13);
        $score=$this->getScoreAlt($cve);
        $obj=[
            "cve"=>$cve,
            "score"=>$score,
            "description"=>$description
        ];
        //echo "CVE SCORE IS: ".$score;
        return $obj;
    }

    private function getScore(string $cve)
    {

// Define the API URL
        $url = "https://cve.circl.lu/api/cve/" . $cve;

// Send a GET request to the API
        $response = file_get_contents($url);
        //echo "RESPONSE: ".$response;
// Check if the response is valid
        if ($response) {
            // Decode the JSON response
            $data = json_decode($response, true);

            // Check if the response has a CVSS score
            if (isset($data["cvss"])) {
                // Get the CVSS score
                $cvss = $data["cvss"];

                // Print the CVSS score
                //echo "The CVSS score of " . $cve . " is " . $cvss . "\n";
                return $cvss;
            } else {
                // Print an error message
                //echo "The CVSS score of " . $cve . " is not found\n";
                return 0;
            }
        } else {
            // Print an error message
            //echo "The API request failed\n";
        }
        return -1;
    }

    public function getResult(){
        return $this->result;
    }

    private function getScoreAlt(string $cve)
    {
        $file=__DIR__."/../cve-summary.csv";
        $handle=fopen($file,"r");
        if($handle){
            while(($row = fgetcsv($handle)) !==false){
                if($row[0]==$cve){
                    $score=0;
                    $severity=strtolower($row[1]);
                    if($severity==='high')
                        $score=10;
                    else if($severity==='medium')
                        $score=5;
                    else if($severity==='low')
                        $score=3;
                    return $score;
                }
            }
        }
        return -1;
    }
}