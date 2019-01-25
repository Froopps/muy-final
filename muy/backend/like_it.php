<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    
    if(!(isset($_POST['voto'])&&isset($_POST['relativoA'])&&isset($_SESSION['email']))||$error_connection['flag']){
        log_into("fail");
        goto error;
    }

    log_into("fail");
    $query="SELECT percorso FROM oggettoMultimediale WHERE extID='".$_POST['relativoA']."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;  
    }

    $_POST['relativoA']=$res->fetch_row()[0];
    $exists="SELECT * FROM valutazione WHERE utente='".escape($_SESSION['email'],$connected_db)."' AND relativoA='".escape($_POST['relativoA'],$connected_db)."'";
    $exists=$connected_db->query($exists);
    if(!$exists){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }
    if(!$exists->fetch_row())
        $query="INSERT INTO valutazione(relativoA,voto,utente) VALUES('".escape($_POST['relativoA'],$connected_db)."','".$_POST['voto']."','".escape($_SESSION['email'],$connected_db)."')";
    else
        $query="UPDATE valutazione SET voto='".$_POST['voto']."' WHERE relativoA='".escape($_POST['relativoA'],$connected_db)."' AND utente ='".escape($_SESSION['email'],$connected_db)."'";
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