<?php
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    if($error_connection["flag"]){
        $value=$error_connection["msg"];
        exit();
    }

    $query="SELECT visualizzazioni FROM `oggettoMultimediale` WHERE extID='".$_GET["id"]."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    $row=$res->fetch_assoc();

    $num=$row["visualizzazioni"]+1;

    $query="UPDATE `oggettoMultimediale` SET visualizzazioni='".$num."' WHERE extID='".$_GET["id"]."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }

    echo $num;
?>