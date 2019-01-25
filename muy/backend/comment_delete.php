<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    if($error_connection["flag"]){
        exit();
    }

    #controllo se altro utente o utente non iscritto sta cercando di eliminare il commmento
    if(!isset($_SESSION["email"])||$_POST["email"]!=$_SESSION["email"]){
        #accesso negato
        echo "denied";
        exit();
    }

    $query="SELECT * FROM `commento` WHERE id='".$_POST["id"]."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    $row=$res->fetch_assoc();
    if($res->num_rows==0||$row["utente"]!=$_POST["email"]){
        #accesso negato
        echo "denied";
        exit();
    }

    $query="DELETE FROM `commento` WHERE id='".$_POST["id"]."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    echo "ok";
?>