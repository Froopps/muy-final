<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    $res=get_content_by_id($_POST["id"],$connected_db);
    if(!$res||$res->num_rows!=1)
        exit();
    $path=$res->fetch_assoc()["percorso"];

    if($error_connection["flag"])
        exit();

    #controllo se altro utente o utente non iscritto sta cercando di eliminare tag
    $query="SELECT proprietario FROM `oggettoMultimediale` WHERE percorso='".escape($path,$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    $row=$res->fetch_assoc();
    if(!isset($_SESSION["email"])||$row["proprietario"]!=$_SESSION["email"]){
        #accesso negato
        echo "denied";
        exit();
    }

    echo $query="DELETE FROM `contenutoTaggato` WHERE tag='".escape($_POST["tag"],$connected_db)."' AND oggetto='".escape($path,$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }

    #check and delete unassigned tags
    $query="DELETE FROM `categoria` WHERE tag NOT IN (SELECT tag FROM contenutoTaggato)";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }

    $connected_db->close();
?>