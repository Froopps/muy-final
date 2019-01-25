<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    #avoid http proxy intrusion and any other homemad request
    $valid_attributes=array('email','passwd','nome','cognome','nickname','sesso','citta','old_pwd');
    echo "<?xml version='1.0' encoding='UTF-8'?>";
    header("Content-type: text/xml; charset=utf-8");

    if($error_connection["flag"]){
        $value=$error_connection["msg"];
        goto error;
    }

    if(!in_array($_POST['attribute'],$valid_attributes)){
        $value="L'aggiornamento può essere fatto solo su valori validi";
        goto error;
    }

    switch($_POST["attribute"]){
        case 'email':
            $res=valid_new_email($_POST['value'],$connected_db);
            $value=($res['error']) ? $res["msg"] : 1;
            break;
        case 'old_pwd':
            $query="SELECT passwd FROM utente WHERE email='".escape($_SESSION['email'],$connected_db)."'";
            $res=$connected_db->query($query);
            if(!$res)
                goto error;
            if(!hash_match($_POST['value'],$res->fetch_assoc()['passwd'])){
                $value='Utente non identificato';
                goto error;
            }
            else{
                #per garantire l'effettiva consequenzialità delle operazioni di inserimento della nuova password 
                #solo dopo l'inserimento della precedente (rendendo la modifica della password sicura rispetto a richieste http
                #inviate al server per vie traverse o fatte da un utente maligno
                #in possesso del dispositivo dell'utente reale) si introduce un flag di sessione.
                echo "<error triggered='false'><message></message></error>";
                $_SESSION['true_user']=1;
                exit();
            }
        case 'passwd':
            if(!isset($_SESSION['true_user'])){
                $value="Utente non identificato";
                goto error;
            }
            $value=(strlen($_POST['value'])<8||!isset($_SESSION['true_user'])) ? 'Password non valida' : 1;
            if($value==1)
                unset($_SESSION['true_user']);
            break;
        case 'sesso':
            $value=!($_POST['value']=='Maschio'||$_POST['value']=='Femmina') ? 'Stringa non valida' : 1;
            break;
        case 'nickname':
            $_POST['value']=$_POST['value']=="" ? "User" : $_POST['value'];
            $_SESSION['nome']=$_POST['value'];
            $value=1;
            break;
        case 'citta':
            $value=!(norm_pattern($_POST['value'])||$_POST['value']=="") ? 'Stringa non valida' :1;
            break;
        default:
            $value=!(norm_pattern($_POST['value'])) ? 'Stringa non valida' : 1;
            break;
    }

    if($value==1){
        if($_POST['attribute']=='passwd')
        $query="UPDATE utente SET ".$_POST["attribute"]."='".blowhash($_POST["value"])."' WHERE email='".escape($_SESSION["email"],$connected_db)."'";
        else
            $query="UPDATE utente SET ".$_POST["attribute"]."='".escape($_POST["value"],$connected_db)."' WHERE email='".escape($_SESSION["email"],$connected_db)."'";
        $res=$connected_db->query($query);
        $connected_db->close();
    }

    else goto error;

    if($res==NULL){
        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
        $value="Errore nella connessione con il database";
        goto error;
    }
    if($_POST['attribute']=='email') $_SESSION['email']=$_POST['value'];

    echo "<error triggered='false'><message></message></error>";
    exit();
    
    error:
        echo "<error triggered='true'><message>".$value."</message></error>";

    function norm_pattern($str){
        return preg_match('/^[A-Za-z\'èéàòù ]+$/',$str);
    }

?>