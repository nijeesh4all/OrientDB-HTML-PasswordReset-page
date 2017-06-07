<?php
include './phpIncludes/dbh.php';


//require('OrientDB/OrientDB.php');
require './PhpOrient/vendor/autoload.php';
use PhpOrient\PhpOrient;

   


//Used for insert and other non value returning operations just send the query to execute

function runInsertQuery($query) {

      $client = new PhpOrient();
    $client->hostname = 'localhost';
    $client->port = 2424;
    $client->username = 'root';
    $client->password = 'hello';
    $client->connect();
    $client->dbOpen('tabe');


    $result = $client->command($query);
    
    
    
    return $result;
}

function getId($table, $field, $value) {

    $client = new PhpOrient();
    $client->hostname = 'localhost';
    $client->port = 2424;
    $client->username = 'root';
    $client->password = 'hello';
    $client->connect();
    $client->dbOpen('tabe');

    $query = "select from $table where $field='$value'";
    $result = $client->command($query);

    $json = json_decode(json_encode($result));

    //var_dump($json);

    if (sizeof($json) > 0) {
        return $json->rid;
    } else {
        return false;
    }
}

function runSelectQuery($query) {

    $client = new PhpOrient();
    $client->hostname = 'localhost';
    $client->port = 2424;
    $client->username = 'root';
    $client->password = 'hello';
    $client->connect();
    $client->dbOpen('tabe');


    $result = $client->query($query);

    //var_dump($result);

    $json = json_decode(json_encode($result));



    if (sizeof($json) > 0) {
        return $json[0]->oData;
    } else {
        return false;
    }
}

function login($user, $pass) {
    $query = "SELECT from login where email ='$user'";

    $answer = runSelectQuery($query);

    if ($answer->Password == $pass) {
        return $answer->Name;
    } else {
        return false;
    }
}


function entryExists($table, $field, $data) {
    
    $query = "SELECT $field FROM $table where $field = '$data'";
    //echo $query;
    $result = runSelectQuery($query);
    
    if ($result != false) {
        return true;
    } else {
        return false;
    }
}

function getByRid($rid) {
    $query = "Select from $rid";
    $result = runSelectQuery($query);

    if ($result) {
        return $result;
    } else {
        return false;
    }
}
function updateRecord($table,$insertField,$insertData,$searchField,$searchData)
{
    
    $query = "UPDATE $table SET $insertField = '$insertData' WHERE $searchField = '$searchData'";
    //echo  $query;
    runInsertQuery($query);
    
    
    
}


function generateId($numb = 4,$type='school')
{
    $random     = '';
    $noVowelscharacters = "BCDFGHJKLMNPQRSTUVWXZ123456789";
    $all = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    
    switch ($type)
    {
        case 'school':
            $characters = $noVowelscharacters;
            break;
        case 'password':
            $characters = $all;
            break;
        
    }
    
    for ($i = 0; $i < $numb; $i++) {
        $random = $random . $characters[rand(0, strlen($characters) - 1)];
    }     
    
    
    return $random;
}
function createLink($relation,$ridFrom,$ridTo)
{
    runInsertQuery("create edge $relation from $ridFrom to $ridTo");
}
function getCount($table,$rid)
{
    $query = "select count(in) from $table where in = $rid";
    $result = runSelectQuery($query);
    
    $json = json_decode(json_encode($result));
    
    
    return (int) $json->count;
}

function csvPrint($target_file)
{
    $content = file_get_contents($target_file);
    $content = trim($content);
    $lines = explode("
",$content);
    $i =false;
    //try{
    echo '<div hidden id=\'csvData\'>';
foreach ($lines as $line)
{
    
    if($i)
    {
        
        $field = explode(',',$line);
        echo $field[0].', '.$field[1].', '.$field[2].', '.$field[3].' ,'.$field[4].';';
    }
    
    $i = true;
    
}
  echo '</div>';  //}
    
}
function writeCsv($target_file,$areaRid,$schoolRid)
{
    $content = file_get_contents($target_file);
    //echo $content;
    $content = trim($content);
    $lines = explode("
",$content);
    //echo sizeof($lines);
    //try{
    
    
    echo '<div>';
    
for ($j =1;$j<sizeof($lines)-1;$j=$j+1)
{
    
    $field = explode(',',$lines[$j]);
    
   
        
        
        
        $query = "INSERT INTO Student (Name,Dob,Aim,Gname,GPhone,studentId,Area,registerdSchool) VALUES ('$field[0]', '$field[1]','$field[2]','$field[3]','$field[4]','$field[4]',$areaRid,$schoolRid)";
        
        //echo $query.'<br>';
        
        runInsertQuery($query);
        
            
    
    
    
}
  echo '</div>';  //}
    
    
}
