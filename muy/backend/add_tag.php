<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");


    $res=get_content_by_id($_POST["id"],$connected_db);
    if(!$res||$res->num_rows!=1)
        exit();
    $path=$res->fetch_assoc()["percorso"];

    if($error_connection["flag"])
        exit();

    #controllo se altro utente o utente non iscritto sta cercando di mettere tag
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

    if(empty($_POST["tag"])){
        #inserisci un tag
        echo "no_tag";
        exit();
    }

    #create tag
    if($_POST["tag"][0]=="#"){
        $_POST["tag"]=substr($_POST["tag"],1);
    }
    $tags=explode("#",$_POST["tag"]);
    $tag=$tags[0];
    if(!($tag=="")){
        trimSpace($tag);
        $tag=strtolower($tag);
        if(!preg_match('/^[A-Za-z0-9\'èéàòùì!? ]+$/',$tag)){
            #tag non accettabile
            echo "err_tag";
            exit();
        }
        $query="SELECT * FROM `categoria` WHERE tag='#".escape($tag,$connected_db)."'";
        $res=$connected_db->query($query);
        if(!$res){
            echo "err_db";
            log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
            exit();
        }
        $row=$res->fetch_assoc();
        if(empty($row)){
            $query="INSERT INTO categoria (tag) VALUES ('#".escape($tag,$connected_db)."')";
            $res=$connected_db->query($query);
            if(!$res){
                echo "err_db";
                log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                exit();
            }
        }
        
        $query="SELECT * FROM `contenutoTaggato` WHERE tag='#".escape($tag,$connected_db)."' AND oggetto='".escape($path,$connected_db)."'";
        $res=$connected_db->query($query);
        if(!$res){
            echo "err_db";
            log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
            exit();
        }
        $row=$res->fetch_assoc();
        if(empty($row)){
             $query="INSERT INTO contenutoTaggato (tag,oggetto,dataAssegnamento) VALUES ('#".escape($tag,$connected_db)."','".escape($path,$connected_db)."','".date('Y-m-d H:i:s')."')";
             $res=$connected_db->query($query);
             if(!$res){
                echo "err_db";
                log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                exit();
            }
        }else{
            echo "tag_dup";
            exit();
        }
    }
    $connected_db->close();
    echo $tag;
?>