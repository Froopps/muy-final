<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    $error="Errore nella connessione con il server";

    if(!(isset($_GET['action'])&&$_GET['next']&&isset($_SESSION['email'])&&!$error_connection['flag']))
        goto error;

    switch ($_GET['action']){

        #REFRESH DELLA TABELLA RICHIESTE PENDENTI
        case 'pending':
            $res=get_pending_request($_SESSION['email'],$_GET['next'],$connected_db);
            if(!$res)
                goto error;
            #stampo la tabella,vedi la funzione in display functions
            display_friendslist_rows($res,$_GET['next']+1,'pending',$connected_db);
            break;

        #REFRESH DELLA TABELLA DELLE AMICIZIE
        case 'friends':
            $res=get_friends($_SESSION['email'],$_GET['next'],$connected_db);
            if(!$res)
                goto error;
            #stampo la tabella,vedi la funzione in display functions
            display_friendslist_rows($res,$_GET['next']+1,'friends',$connected_db);
            break;

        #REFRESH DELLA TABELLA DEI SUGGERIMENTI
        case 'suggest':
            $res=get_suggestions_by_city($_SESSION['email'],$_GET['next'],$connected_db);
            if(!$res)
                goto error;
            #stampo la tabella,vedi la funzione in display functions
            display_friendslist_rows($res,$_GET['next']+1,'suggest',$connected_db);
            break;
    }
    
    exit();

    error:
        echo "<div class='error_div'><span class='error_span'>".$error."</span></div>";

?>