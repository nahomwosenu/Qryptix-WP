<?php
class Persist
{
    static $ENDPOINT="http://localhost/";
    static $server='localhost';
    static $user='nahom';
    static $pass='AlphaGeek23';
    static $db='qryptix_wp';
    static function getType($data){
        $type = gettype($data);

        // Switch on the type
        switch ($type) {
            // If the type is boolean, append i to the types
            case "boolean":
                return "i";
                break;
            // If the type is integer, append i to the types
            case "integer":
                return "i";
                break;
            // If the type is double, append d to the types
            case "double":
                return  "d";
                break;
            // If the type is string, append s to the types
            case "string":
                return  "s";
                break;
            // If the type is any other type, append b to the types
            default:
                return  "s";
                break;
        }
    }
    static function save($object,$entity){
        $array=json_encode($object);
        $array2=json_decode($array,true);
        //print_r($array2);
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        mysqli_set_charset($con,'utf8');
        $columns=implode(",", self::getColumns($array2));
        //var_dump($columns);
        $list=array();
        $dtypes=array();
        $data=self::getData($array2);
        for($i=0;$i<count(self::getColumns($array2));$i++){
            $list[$i]="?";
            $dtypes[$i]=self::getType($data[$i]);
            //$data[$i]=$con->real_escape_string($data[$i]);
        }
        $keys=implode(",",$list);
        //print_r($data);
        //$keys="'".$keys."'";
        $query="insert into $entity ($columns) values($keys)";
        //echo "<p>Query: $query</p>";
        $p=mysqli_prepare($con,$query) or die(mysqli_error($con));
        $p->bind_param(implode($dtypes),...$data);
        $p->execute() or die(mysqli_error($con));
        $id=$p->insert_id;
        //echo $p->error;
        return $id;
    }
    static function escape($str){
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        $str=mysqli_real_escape_string($con,$str);
        return $str;
    }
    static function getColumns($object){
        $columns=array();
        $i=0;
        foreach($object as $key=>$value){
            if($key=='id' || $key=='ID' || $key=='meta_id')
                continue;
            $columns[$i]=$key;
            $i++;
        }
        return $columns;
    }
    static function getData($object){
        $columns=array();
        $i=0;
        foreach($object as $key=>$value){
            if($key=='id' || $key=='ID' || $key=='meta_id')
                continue;
            $columns[$i]=$value;
            $i++;
        }
        return $columns;
    }
    static function update($object,$entity){
        $columns=array();
        $values=array();
        $i=0;
        $array=json_encode($object);
        $array2=json_decode($array,true);
        $builder="";
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        foreach($array2 as $key=>$value){
            $columns[$i]=$key;
            $value=$con->real_escape_string($value);
            $values[$i]=$value;
            if($i==0)
                $builder=$key."='".$value."'";
            else
                $builder=$builder.','.$key."='".$value."'";
            $i++;
        }
        $query="update $entity set $builder where id='$values[0]'";
        $st=mysqli_prepare($con,$query) or die(mysqli_error($con));
        $result=$st->execute();
        return $st->affected_rows;
    }
    static function getEntity($keys,$values,$connector,$entity,$class){
        $Class="Class".$class;
        $obj=new $Class;
        $condition="";
        for($i=0;$i<count($keys);$i++){
            if($i==0)
                $condition=$keys[$i]."='".$values[$i]."'";
            else
                $condition=$condition." ".$connector." ".$keys[$i]."='".$values[$i]."'";
        }
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        $query="select * from $entity where $condition";
        $p=mysqli_prepare($con,$query) or die(mysqli_error($con));
        $r=$p->execute();
        $result=$p->get_result();
        $row=$result->fetch_assoc();
        return $row;
    }
    static function executeQuery($query){
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
        $list=array();
        $i=0;
        while($row=mysqli_fetch_assoc($result)){
            foreach ($row as $key=>$value){
                $list[$i][$key]=$value;
            }
            $i++;
        }
        return $list;
    }
    static function executeUpdate($query){
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
        return mysqli_affected_rows($con);
    }
    static function  exists($key,$value,$entity){
        $query="select $key from $entity where $key='$value'";
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
        if($row=mysqli_fetch_array($result)){
            return true;
        }
        return false;
    }
    static function checkDuplicateUsername($username){
        $bool=self::exists("username",$username,"admin");
        if($bool===false){
            $bool=self::exists("student_id",$username,"student");
            if($bool===false){
                $bool=self::exists("email",$username,"student");
                if($bool===false){
                    $bool=self::exists("email",$username,"counceler");
                    if($bool===false){
                        $bool=self::exists("email",$username,"director");
                    }
                }
            }
        }
        return $bool;
    }
    static function checkDuplicate($field,$value){
        $bool=self::exists($field,$value,"student");
        if($bool===false){
            $bool=self::exists($field,$value,"counceler");
            if($bool===false){
                $bool=self::exists($field,$value,"director");
            }
        }
        return $bool;
    }
    static function getVerificationLink($id,$hash,$entity){
        $link=self::$ENDPOINT."/verify.php?token=$hash&id=$id&type=$entity";
        return $link;
    }
    static function verifyEmail($id,$entity){
        $query="update $entity set status='verified' where id='$id'";
        $r=self::executeUpdate($query);
        return $r;
    }
    static function getResetLink($id,$hash,$entity){
        $link=self::$ENDPOINT."/reset.php?token=$hash&id=$id&type=$entity";
        return $link;
    }

    public static function query($query, ...$values)
    {
        $con=mysqli_connect(self::$server,self::$user,self::$pass,self::$db) or die(mysqli_error($con));
        try {
            // Prepare the statement with the query
            $stmt = $con->prepare($query);

            // Check if the statement is prepared
            if ($stmt) {
                // Create a string to store the types of the values
                $types = "";

                // Loop through the values
                foreach ($values as $value) {
                    // Get the type of the value
                    $type = gettype($value);

                    // Switch on the type
                    switch ($type) {
                        // If the type is boolean, append i to the types
                        case "boolean":
                            $types .= "i";
                            break;
                        // If the type is integer, append i to the types
                        case "integer":
                            $types .= "i";
                            break;
                        // If the type is double, append d to the types
                        case "double":
                            $types .= "d";
                            break;
                        // If the type is string, append s to the types
                        case "string":
                            $types .= "s";
                            break;
                        // If the type is any other type, append b to the types
                        default:
                            $types .= "b";
                            break;
                    }
                }

                // Bind the types and the values to the statement
                $stmt->bind_param($types, ...$values);

                // Execute the statement
                $stmt->execute();

                // Get the result set
                $rs = $stmt->get_result();

                // Create a list to store the result
                $result = array();

                // Loop through the result set
                while ($row = $rs->fetch_assoc()) {
                    // Add the row to the result
                    array_push($result, $row);
                }

                // Free the result set
                $rs->free();

                // Close the statement
                $stmt->close();

                // Return the result
                return $result;
            } else {
                // Throw an exception if the statement is not prepared
                throw new Exception("Prepare failed: " . $con->error);
            }
        } catch (Exception $e) {
            // Handle any exceptions
            echo $e->getMessage();
            // Return null if any error occurs
            return null;
        }
    }

}
