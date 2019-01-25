<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    $redirect_with_error="Location:$location/frontend/user.php?user=".urlencode($_SESSION['email'])."&error=";
    $error="Errore nella connessione con il server";
    $user_dir=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res/content/".$_SESSION["email"];
    $path=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res";
    if(!isset($_SESSION['email'])||$error_connection['flag'])
        goto error;
    $query="SELECT COUNT(*) FROM amicizia WHERE (sender='".escape($_SESSION['email'],$connected_db)."' OR receiver='".escape($_SESSION['email'],$connected_db)."') AND (stato IS NULL OR stato='a')";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore nell'esecuzione della query ".$query." ".$connected_db->error);
        goto error;
    }
    $row=$res->fetch_row();
    if($row[0]!=0){
        $error="Per eliminarti è necessario che tu non abbia richieste di amicizia pendenti nè alcuna amicizia corrente";
        goto error;
    }

    #delete folders
    $query="SELECT * FROM `oggettoMultimediale` WHERE proprietario='".escape($_SESSION["email"],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }
    if($res->num_rows>0){
        while($row=$res->fetch_assoc()){
            if($row["anteprima"][1]=="c")
                unlink($path.$row["anteprima"]);
            if($row["percorso"][0]!="h"){
                unlink($path.$row["percorso"]);
                rmdir($path.$row["percorso"]."/../");
            }else
                rmdir($path."/content/".$row["proprietario"]."/".$row["canale"]."/".getYoutubeId($row["percorso"]));
        }
    }
    $query="SELECT * FROM `canale` WHERE proprietario='".escape($_SESSION["email"],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }
    if($res->num_rows>0){
        while($row=$res->fetch_assoc())
            rmdir($user_dir."/".$row["nome"]);
    }

    $query="DELETE FROM amicizia WHERE sender='".escape($_SESSION['email'],$connected_db)."' OR receiver='".$_SESSION['email']."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore nell'esecuzione della query ".$query." ".$connected_db->error);
        goto error;
    }
    
    $query="DELETE FROM utente WHERE email='".escape($_SESSION['email'],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore nell'esecuzione della query ".$query." ".$connected_db->error);
        goto error;
    }
    rmdir($user_dir);

    $utente=$_SESSION["nome"];
    session_destroy();
    header("Location:$location/frontend/home.php?msg=Addio, ".$utente);
    exit();
    error:
        $redirect_with_error.=urlencode($error);
        header($redirect_with_error);
        exit();
?>