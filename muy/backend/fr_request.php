<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    #avoid http proxy intrusion and any other homemade request
    $error="Errore nella connessione con il server";
    header("Content-type: text/xml; charset=utf-8");
    echo "<?xml version='1.0' encoding='UTF-8'?>";

    if(!(isset($_POST['receiver'])&&isset($_SESSION['email'])&&!$error_connection['flag']))
        goto error;

    $res=get_user_by_email($_POST['receiver'],$connected_db);

    if(!$res||$res->fetch_assoc()['COUNT(*)']<=0)
        goto error;

    $res=get_relationship($_SESSION['email'],$_POST['receiver'],$connected_db);
    #only if there are no entry in the friendship table the request is valid
    if(!$res||$res!='no')
        goto error;

    $query="INSERT INTO amicizia (sender,receiver) VALUES ('".escape($_SESSION['email'],$connected_db)."','".escape($_POST['receiver'],$connected_db)."')";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore nell'esecuzione della query ".$query." ".$connected_db->error);
        goto error;
    }
    echo "<error triggered='false'><message></message></error>";
    exit();

    error:
        echo "<error triggered='true'><message>".$error."</message></error>";

?>