<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    $redirect_with_error="Location:$location/frontend/home.php?error=";
    if($error_connection["flag"]){
        $redirect_with_error.=urlencode($error_connection["msg"]);
        header($redirect_with_error);
        exit();
    }
    $email="'".$connected_db->real_escape_string($_POST["login"])."'";
    $query="SELECT COUNT(*),email,passwd,nickname,foto,sesso FROM utente WHERE email=".$email;
    $res=$connected_db->query($query);
    if(!$res){
        $redirect_with_error.=urlencode("Errore nella connessione con il database");
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }
    $row=$res->fetch_row();
    if($row[0]==0){
        $redirect_with_error.=urlencode("Email o password errati");
        goto error;
    }
    if(!hash_match($_POST["pwd"],$row[2])){
        $redirect_with_error.="Password sbagliata".$res->fetch_row()[1];
        goto error;
    }
    $_SESSION["email"]=stripslashes($row[1]);
    $_SESSION["nome"]=$row[3];
    $_SESSION["foto"]=$row[4];
    #just for developement test, in signup.php too

    $redirect_with_msg="Location:$location/frontend/home.php?msg=".urlencode("Ciao ".$_SESSION["nome"].", bentornat");
    if($row[5]=="Maschio")
        $redirect_with_msg.="o!";
    else
        $redirect_with_msg.="a!";
    header($redirect_with_msg);
    $connected_db->close();
    exit();
    error:
        header($redirect_with_error);
        $connected_db->close();
        exit();
?>