<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    $redirect_with_error="Location:$location/frontend/upload.php?error=";
    $redirect_with_msg="Location:$location/frontend/user.php?user=".urlencode($_SESSION["email"])."&msg=".urlencode("Upload youtube avvenuto con successo");
    $query_columns="";
    $query_values="";
    #exit() is used after redirect to avoid further statements execution after redirecting with error
    if($error_connection["flag"]){
        $redirect_with_error.=urlencode($error_connection["msg"]);
        goto error;
    }

    if(empty($_POST["url"])||empty($_POST["channel"])||empty($_POST["title"])){
        $redirect_with_error.=urlencode("Inserisci tutti i dati richiesti");
        goto error;
    }
    $result=explode("www.youtu",$_POST["url"]);
    if(!isset($result[1])){
        $redirect_with_error.=urlencode("URL non valido");
        goto error;
    }
    $_POST["url"]=trimSpaceBorder($_POST["url"]);

    #vedi functions.php
    $id=getYoutubeId($_POST["url"]);
    $dir="/content/".$_SESSION["email"]."/".$_POST["channel"]."/".$id;
    $thumbnail="http://img.youtube.com/vi/".$id."/hqdefault.jpg";
    $immagine="data:image/png;base64,".base64_encode(file_get_contents($thumbnail));

    #percorso
    $query="SELECT percorso FROM oggettoMultimediale WHERE canale='".escape($_POST["channel"],$connected_db)."' AND proprietario='".escape($_SESSION["email"],$connected_db)."' AND percorso='".escape($_POST["url"],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        $redirect_with_error.=urlencode("Errore nella connessione con il database");
        goto error;
    }
    if($res->num_rows>0){
        $redirect_with_error.=urlencode("Hai già questo video sul canale");
        goto error;
    }
    $query_values.="'".escape($_POST["url"],$connected_db)."',";
    $query_columns.="percorso,";
    #anteprima

    $query_values.="'".escape($dir,$connected_db)."/anteprima.png',";
    $query_columns.="anteprima,";
    #titolo
    if(!preg_match('/^[A-Za-z0-9\'èéàòùì!?-_.:,; ]+$/',$_POST["title"])){
        $redirect_with_error.=urlencode("Titolo non accettabile");
        goto error;
    }
    if(strlen($_POST["title"]>200)){
        $redirect_with_error.=urlencode("Titolo troppo lungo");
        goto error;
    }
    $query_values.="'".escape($_POST["title"],$connected_db)."',";
    $query_columns.="titolo,";
    #descrizione
    if(!empty($_POST["desc"])){
        if(!preg_match('/^[A-Za-z0-9\'èéàòùì!?-_.:,; ]+$/',$_POST["desc"])){
            $redirect_with_error.=urlencode("Descrizione non accettabile");
            goto error;
        }
        if(strlen($_POST["desc"])>pow(2,24)-1){
            $redirect_with_error.=urlencode("Descrizione troppo lunga");
            goto error;
        }
    $query_values.="'".escape($_POST["desc"],$connected_db)."',";
    $query_columns.="descrizione,";
    }
    #tipo
    $query_values.="'v',";
    $query_columns.="tipo,";
    #dataCaricamento
    $query_values.="'".date('Y-m-d H:i:s')."',";
    $query_columns.="dataCaricamento,";
    #canale
    #controllo canale valido per checking anyone sending post without the signup form?
    $query_values.="'".escape($_POST["channel"],$connected_db)."',";
    $query_columns.="canale,";
    #proprietario
    #controllo se mail è valida?
    $query_values.="'".escape($_SESSION["email"],$connected_db)."'";
    $query_columns.="proprietario";

    $query="INSERT INTO oggettoMultimediale (".$query_columns.") VALUES (".$query_values.")";
    $res=$connected_db->query($query);
    if(!$res){
        $redirect_with_error.=urlencode("Errore nella connessione con il database");
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }

    mkdir($_SERVER["DOCUMENT_ROOT"]."/muy/muy_res".$dir,0770);
    ritaglia($immagine,$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res".$dir."/anteprima.png");

    $query="UPDATE canale SET dataUltimoInserimento='".date('Y-m-d H:i:s')."' WHERE nome='".escape($_POST["channel"],$connected_db)."' AND proprietario='".escape($_SESSION["email"],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        $redirect_with_error.=urlencode("Errore nella connessione con il database");
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }

    #etichette
    if(!(empty($_POST["tag"]))){
        if($_POST["tag"][0]=="#"){
            $_POST["tag"]=substr($_POST["tag"],1);
        }
        $tags=explode("#",$_POST["tag"]);
        foreach($tags as $tag){
            if(!($tag=="")){
                trimSpace($tag);
                $tag=strtolower($tag);
                if(!preg_match('/^[A-Za-z0-9\'èéàòùì!? ]+$/',$tag)){
                    $redirect_with_msg.=urlencode(", ma uno o più tag non accettabili");
                    goto error;
                }

                #controllo esistenza categoria
                $query="SELECT * FROM `categoria` WHERE tag='#".escape($tag,$connected_db)."'";
                $res=$connected_db->query($query);
                if(!$res){
                    $redirect_with_error.=urlencode("Errore nella connessione con il database 2");
                    log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                    goto error;
                }
                $row=$res->fetch_assoc();
                if(empty($row)){
                    $query="INSERT INTO categoria (tag) VALUES ('#".escape($tag,$connected_db)."')";
                    $res=$connected_db->query($query);
                    if(!$res){
                        $redirect_with_error.=urlencode("Errore nella connessione con il database 3");
                        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                        goto error;
                    }
                }
                $query="SELECT * FROM `contenutoTaggato` WHERE tag='#".escape($tag,$connected_db)."' AND oggetto='".escape($_POST["url"],$connected_db)."'";
                $res=$connected_db->query($query);
                if(!$res){
                    echo "err_db";
                    log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                    exit();
                }
                $row=$res->fetch_assoc();
                if(empty($row)){
                     $query="INSERT INTO contenutoTaggato (tag,oggetto,dataAssegnamento) VALUES ('#".escape($tag,$connected_db)."','".escape($_POST["url"],$connected_db)."','".date('Y-m-d H:i:s')."')";
                     $res=$connected_db->query($query);
                     if(!$res){
                        echo "err_db";
                        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                        exit();
                    }
                }
            }
        }
    }
    header($redirect_with_msg);
    $connected_db->close();
    exit();
    error:
        header($redirect_with_error);
        $connected_db->close();
        exit();
?>