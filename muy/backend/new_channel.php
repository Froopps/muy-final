<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    if($error_connection["flag"]){
        exit();
    }
    $query_columns="";
    $query_values="";
    $count=0;
    if(!(isset($_POST["channel_name"]))){
        echo "data_miss";
        exit();
    }

    $query="SELECT COUNT(*) FROM canale WHERE nome='".escape($_POST["channel_name"],$connected_db)."' and proprietario='".escape($_SESSION["email"],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        echo "db_err";
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        exit();
    }
    $row=$res->fetch_row();
    if($row[0]>0){
        echo "duplicate";
        exit();
    }
    if(strlen($_POST["channel_name"])>200){
        echo "long";
        exit();
    }
    $query_columns.="proprietario,nome";
    $query_values.="'".escape($_SESSION["email"],$connected_db)."','".escape($_POST["channel_name"],$connected_db)."'";

    if(!in_array($_POST["channel_type"],array("public","social","private"))){
        echo "type_err";
        exit();
    }

    $query_columns.=",visibilita";
    $query_values.=",'".$_POST["channel_type"]."'";

    if(!empty($_POST["label"])){
        $_POST["label"]=preg_replace('/,+/',',',$_POST["label"]);
        $_POST["label"]=preg_replace('/\s*,\s*/',',',$_POST["label"]);
        $_POST["label"]=trimSpace($_POST["label"]);
        $_POST["label"]=strtolower($_POST["label"]);
        while($_POST["label"][0]==",")
            $_POST["label"]=substr($_POST["label"],1);
        while($_POST["label"][strlen($_POST["label"])-1]==",")
            $_POST["label"]=substr($_POST["label"],0,-1);
        if($_POST["label"]!=""&&$_POST["label"]!=" "){
            $query_columns.=",etichetta";
            $query_values.=",'".escape($_POST["label"],$connected_db)."'";
        }
    }

    $query_columns.=",dataCreazione";
    $query_values.=",'".date('Y-m-d',time())."'";

    $query="INSERT INTO canale (".$query_columns.") VALUES (".$query_values.")";
    $res=$connected_db->query($query);
    if(!$res){
        echo "db_err";
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        exit();
    }
    mkdir($_SERVER["DOCUMENT_ROOT"]."/muy/muy_res/content/".$_SESSION["email"]."/".$_POST["channel_name"],0770);
    echo ($_SESSION["email"]);
?>