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
    public static function executeCommand($cmd){
        return shell_exec($cmd);
        /*$timeout = 30;
        $descriptor = array(
            0 => array("pipe", "r"), // stdin
            1 => array("pipe", "w"), // stdout
            2 => array("pipe", "w")  // stderr
        );
// Open the process and get the handle
        $process = proc_open($cmd, $descriptor, $pipes);

// Check if the process is opened successfully
        if (is_resource($process)) {
            // Get the start time
            $output=stream_get_contents($pipes[1]);
            $start = time();

            // Loop until the timeout is reached or the process is finished
            while (time() - $start < $timeout) {
                // Get the status of the process
                $status = proc_get_status($process);

                // Check if the process is still running
                if ($status["running"]) {
                    // Sleep for a while
                    sleep(1);
                } else {
                    // Break the loop
                    break;
                }
            }

            // Terminate the process
            proc_terminate($process);

            // Close the process
            proc_close($process);
            return $output;
        } else {
            // Print an error message
            //echo "The process could not be opened\n";
            return "";
        }*/
    }
}