<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    $redirect_with_error="Location:$location/frontend/home.php?error=";
    if($error_connection["flag"]){
        $redirect_with_error.=urlencode($error_connection["msg"]);
        header($redirect_with_error);
        exit();
    }
    if(!isset($_SESSION["email"])){
        $rediret_with_error.=urlencode("Accesso negato");
        header($redirect_with_error);
        exit();
    }

?>

<!DOCTYPE HTML>
<html>

<head>
    <title>MUY | Amici</title>
    
    <?php include "../common/head.php"; ?>
</head>

<body>
    <?php
        include "../common/header_logged.php";
        include "../common/sidebar_logged.php";
    ?>
    <main>
        <div class="content">
            <button class='in_notext' id='delete' type='button' onclick='del_pending(this)'>Ritira tutte le richieste</button>
            <div class="headingArea">
                <h2 style='margin-top:10%'>Richieste</h2>
            </div>
            <div class="friend_list_tb pending_view">
            <?php
                    #la funzione in getter_functions prende i dati di tutti gli utenti che hanno inviato una richiesta
                    #all'utente loggato
                    $res=get_pending_request($_SESSION['email'],0,$connected_db);
                    if(!$res){
                        echo "<span class='error_span>Errore nella connessione col server</span>";
                        exit();
                    }
                    if($res->num_rows==0)
                        echo "<div class='error_div'><span class='message_span'>Nessuna richiesta per te in attesa di conferma</span></div>";
                    #stampo la tabella,vedi la funzione in display functions
                    display_friendslist_rows($res,1,'pending',$connected_db);
            ?>
            </div>
            <div class="headingArea">
                <h2 style='margin-top:30px;'>Suggeriti per città</h2>
            </div>
            <div class="friend_list_tb suggestions_view">
            <?php
                #prendo le amicizie correnti dalla tabella amicizia, vedi query in getter functions
                $res=get_suggestions_by_city($_SESSION['email'],0,$connected_db);
                #se la query fallisce log e redirect con segnalazione
                if(!$res){
                    echo "<span class='error_span>Errore nella connessione col server</span>";
                    exit();
                }
                #verifico che effettivamente l'utente abbia ramicizie correnti
                if($res->num_rows==0)
                    echo "<div class='error_div'><span class='message_span'>Nessun suggerimento per te</span></div>";
                #stampo la tabella,vedi la funzione in display functions
                display_friendslist_rows($res,1,'suggest',$connected_db);
            ?>
            </div>
            <div class="headingArea">
                <h2 style='margin-top:30px;'>Amici</h2>
            </div>
            <div class="friend_list_tb friends_view">
            <?php
                #prendo le amicizie correnti dalla tabella amicizia, vedi query in getter functions
                $res=get_friends($_SESSION['email'],0,$connected_db);
                #se la query fallisce log e redirect con segnalazione
                if(!$res){
                    echo "<span class='error_span>Errore nella connessione col server</span>";
                    exit();
                }
                #verifico che effettivamente l'utente abbia ramicizie correnti
                if($res->num_rows==0)
                    echo "<div class='error_div'><span class='message_span'>Nessuna amicizia</span></div>";
                #stampo la tabella,vedi la funzione in display functions
                display_friendslist_rows($res,1,'friends',$connected_db);
            ?>
            </div>
        </div>
    </main>
    <script type='text/javascript' src='../common/script/setup.js'></script>
    <script type='text/javascript' src='../common/script/friendship.js'></script>
    <script type="text/javascript" src="../common/script/search.js"></script> 
</body>
</html>