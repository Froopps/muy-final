<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    if($error_connection["flag"])
        exit();

    print_r($_POST);

    $path=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res";

    #controllo se altro utente o utente non iscritto sta cercando di eliminare tag
    if(!isset($_SESSION["email"])||$_SESSION["email"]!=$_POST["proprietario"]){
        echo "denied";
        exit();
    }

    #delete folders
    $query="SELECT * FROM `oggettoMultimediale` WHERE canale='".escape($_POST["nome"],$connected_db)."' AND proprietario='".escape($_POST["proprietario"],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
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
    $query="DELETE FROM `canale` WHERE nome='".escape($_POST["nome"],$connected_db)."' AND proprietario='".escape($_POST["proprietario"],$connected_db)."'";
    $res=$connected_db->query($query);
    if(!$res){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $connected_db->close();
        exit();
    }
    
    log_into($_POST["nome"],$_POST["proprietario"]);
    $ris=rmdir($path."/content/".$_POST["proprietario"]."/".$_POST["nome"]);
    echo "ok";
    $connected_db->close();
?>