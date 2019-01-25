<?php
    #if this file is included remember to check the error connection status
    #realpath convert the pathname considering the current os and its correct symbols
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/functions.php");
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/display_functions.php");
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/getter_functions.php");
    $host="localhost";
    $user="root";
    $passwd="";
    $db="muy";
    $error_connection=array("flag"=>0,"msg"=>"");
    $connected_db=new mysqli($host,$user,$passwd,$db);
    if($connected_db->connect_errno){
        $error_connection["flag"]=1;
        $error_connection["msg"]="Spiacenti, errore nella connessione col database";
        #remember to give apache the permission required
        log_into($connected_db->connect_error);
    }
    $connected_db->set_charset('utf8');
    $location="http://localhost/muy";
?>