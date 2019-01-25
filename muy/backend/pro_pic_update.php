<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    
    echo "<?xml version='1.0' encoding='UTF-8'?>";
    header("Content-type: text/xml; charset=utf-8");
    if(isset($_POST["default"])){
        $q_pro_pic="defaults/default-profile-pic.png";
        $query="UPDATE utente SET foto='".$q_pro_pic."' WHERE email='".escape($_SESSION["email"],$connected_db)."'";
        $res=$connected_db->query($query);
        if($res==NULL){
            log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
            goto error;
        }
        exit();
    }
    if($_FILES["cropped_pro_pic"]["error"]>0){
        log_into($_FILES["cropped_pro_pic"]["error"]);
        goto error;
    }
    #query before saving the result to avoid unused image on server

    $pro_pic=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res/content/".$_SESSION["email"]."/".$_FILES["cropped_pro_pic"]["name"];
    $q_pro_pic=escape("content/".$_SESSION["email"]."/".$_FILES["cropped_pro_pic"]["name"],$connected_db);
    $query="UPDATE utente SET foto='".$q_pro_pic."' WHERE email='".escape($_SESSION["email"],$connected_db)."'";
    
    $res=$connected_db->query($query);
    if($res==NULL){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        goto error;
    }

    #the cropping is made as a change to an existing user
    if(file_exists($pro_pic)){
        unlink($pro_pic);
    }
    $moved=move_uploaded_file($_FILES["cropped_pro_pic"]["tmp_name"],$pro_pic);
    if(!$moved)
        goto error;
    echo "<error triggered='false'><message></message></error>";
    exit();

    error:
        echo "<error triggered='true'><message>Caricamento foto fallito</message></error>";
        $connected_db->close();
        exit();

?>