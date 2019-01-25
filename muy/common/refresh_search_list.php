<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    $error="Errore nella connessione con il server";

    if(!(isset($_GET['action'])&&$_GET['next']&&isset($_GET['pattern'])&&!$error_connection['flag']))
        goto error;

    switch ($_GET['action']){

        /*REFRESH RICERCA OGGETTI MULTIMEDIALI*/
        #vedi getter_functions.php
        #function get_today_friends_content($who,$connected,$offset)
        case('todayContent'):
            $res=get_today_friends_content($_SESSION['email'],$connected_db,$_GET['next']);
            break;

        case('nearFriends'):
            $res=get_near_friends($_SESSION['email'],$connected_db,$_GET['next']);
            break;

        /*REFRESH RICERCA OGGETTI MULTIMEDIALI*/
        #vedi getter_functions.php
        #function get_searched_content($who,$pattern,$connected_db,$offset,$limit=3,$suggestion=false)
        case 'oggettoMultimediale':
            if(!isset($_SESSION['email']))
                $res=get_searched_content('',$_GET['pattern'],$connected_db,$_GET['next']);
            else
                $res=get_searched_content(escape($_SESSION['email'],$connected_db),$_GET['pattern'],$connected_db,$_GET['next']);
            break;

        /*REFRESH DELLA RICERCA DI CANALI*/
        #vedi getter_functions.php
        #function get_searched_channel($who,$pattern,$connected_db,$offset,$limit=3,$suggestion=false)
        case 'canale':
            if(!isset($_SESSION['email']))
                $res=get_searched_channel('',$_GET['pattern'],$connected_db,$_GET['next']);
            else
                $res=get_searched_channel(escape($_SESSION['email'],$connected_db),$_GET['pattern'],$connected_db,$_GET['next']);
            break;
        
        #REFRESH DELLA RICERCA UTENTE E CATEGORIA
        #vedi getter_functions.php
        #function get_public_result($table,$pattern,$connected_db,$offset,$limit=3,$suggestion=false)
        default:
            $res=get_public_result($_GET['action'],$_GET['pattern'],$connected_db,$_GET['next']);
            break;
    }

    if($res){
        #vedi display_functions.php
        #function display_refreshing_block($table,$pattern,$query_result,$offset_of_next_query)
        display_refreshing_block($_GET['action'],$_GET['pattern'],$res,$_GET['next']+1);
        exit();
    }
    error:
        echo "<div class='error_div'><span class='error_span'>".$error."</span></div>";

?>