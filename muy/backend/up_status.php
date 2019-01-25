<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    #avoid http proxy intrusion and any other homemade request
    $error="Errore nella connessione con il server";
    header("Content-type: text/xml; charset=utf-8");
    echo "<?xml version='1.0' encoding='UTF-8'?>";
    #object means opposite to subject
    if(!(isset($_POST['action'])&&isset($_POST['object'])&&isset($_SESSION['email'])&&!$error_connection['flag']))
        goto error;
    $object=$_POST['object'];
    $subject=$_SESSION['email'];
    switch ($_POST['action']){
        case 'accept':
            $res=get_relationship($subject,$object,$connected_db);
            if($res!='pending')
                goto error;
            $query="UPDATE amicizia SET stato='a', dataRisposta='".date('Y-m-d',time())."' WHERE sender='".escape($object,$connected_db)."' AND receiver='".escape($subject,$connected_db)."'";
            $res=$connected_db->query($query);
            if(!$res){
                log_into("Errore di esecuzione della query ".$query." ".$connected_db->error);
                goto error;
            }
            break;
        case 'deny':
            $res=get_relationship($subject,$object,$connected_db);
            if($res!='pending')
                goto error;
            $query="UPDATE amicizia SET stato='r', dataRisposta='".date('Y-m-d',time())."' WHERE sender='".escape($object,$connected_db)."' AND receiver='".escape($subject,$connected_db)."'";
            $res=$connected_db->query($query);
            if(!$res){
                log_into("Errore di esecuzione della query ".$query." ".$connected_db->error);
                goto error;
            }
            break;
        case 'erase':
            $res=get_relationship($subject,$object,$connected_db);
            if($res!='a')
                goto error;
            $query="UPDATE amicizia SET stato='p' WHERE sender='".escape($subject,$connected_db)."' AND receiver='".escape($object,$connected_db)."' OR sender='".escape($object,$connected_db)."' AND receiver='".escape($subject,$connected_db)."'";
            $res=$connected_db->query($query);
            if(!$res){
                log_into("Errore di esecuzione della query ".$query." ".$connected_db->error);
                goto error;
            }
            break;
    }

    echo "<error triggered='false'><message></message></error>";
    exit();

    error:
        echo "<error triggered='true'><message>".$error."</message></error>";

?>