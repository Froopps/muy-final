<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    if(!isset($_SESSION['email'])||$error_connection['flag'])
        goto error;

    $query="DELETE FROM amicizia WHERE sender='".escape($_SESSION['email'],$connected_db)."' AND stato IS NULL";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }
    echo "{\"error\":false}";
    exit();
    error:
        echo "{\"error\":true}";
?>