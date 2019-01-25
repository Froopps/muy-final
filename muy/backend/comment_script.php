<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");


    $res=get_content_by_id($_POST["id"],$connected_db);
    if(!$res||$res->num_rows!=1)
        exit();
    $path=$res->fetch_assoc()["percorso"];

    if($error_connection["flag"])
        exit();

    #controllo se altro utente o utente non iscritto sta cercando di commmentare
    $query="SELECT proprietario FROM `oggettoMultimediale` WHERE percorso='".escape($path,$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    $row=$res->fetch_assoc();
    if(!isset($_SESSION["email"])){
        #accedi
        echo "sign in";
        exit();
    }

    if(empty($_POST["commento"])){
        #inserisci un commento
        echo "no comment";
        exit();
    }

    #max 3 comments per user in every content
    $query="SELECT * FROM `commento` WHERE utente='".escape($_SESSION["email"],$connected_db)."' AND contenuto='".escape($path,$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    $row=$res->fetch_assoc();
    if($res->num_rows>=3){
        #hai commentato troppe volte
        echo "too many";
        exit();
    }

    #create comment
    $query_columns="";
    $query_values="";

    $query_columns.="utente,";
    $query_values.="'".escape($_SESSION["email"],$connected_db)."',";

    $query_columns.="contenuto,";
    $query_values.="'".escape($path,$connected_db)."',";

    $query_columns.="testo,";
    $query_values.="'".escape($_POST["commento"],$connected_db)."',";

    $query_columns.="dataRilascio";
    $query_values.="'".date('Y-m-d H:i:s')."'";

    $query="INSERT INTO `commento` (".$query_columns.") VALUES (".$query_values.")";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }

    #get new comment id for celete script
    $query="SELECT id FROM `commento` WHERE utente='".escape($_SESSION["email"],$connected_db)."' AND contenuto='".escape($path,$connected_db)."' AND dataRilascio='".date('Y-m-d H:i:s')."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    $row=$res->fetch_assoc();
    echo $row["id"];
?>