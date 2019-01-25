<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    $valid=array('utente','oggettoMultimediale','canale','categoria');
    $mapping=array("utente"=>"nickname","oggettoMultimediale"=>"titolo","canale"=>"nome","categoria"=>"tag");
   
    if(!(isset($_GET['table'])&&isset($_GET['pattern'])&&in_array($_GET['table'],$valid)))
        exit();
    switch($_GET['table']){
        #vedi getter_functions.php
        #function get_searched_content($who,$pattern,$connected_db,$offset,$limit=3,$suggestion=false)
        case 'oggettoMultimediale':
            if(!isset($_SESSION['email']))
                $res=get_searched_content('',$_GET['pattern'],$connected_db,0,6,true);
            else
                $res=get_searched_content(escape($_SESSION['email'],$connected_db),$_GET['pattern'],$connected_db,0,6,true);
            break;

        case 'canale':
            #vedi getter functions.php
            #function get_searched_channel($who,$pattern,$connected_db,$offset,$limit,$suggestion=false)
            if(!isset($_SESSION['email']))
                $res=get_searched_channel('',$_GET['pattern'],$connected_db,0);
            else
                $res=get_searched_channel(escape($_SESSION['email'],$connected_db),$_GET['pattern'],$connected_db,0,6,true);
            break;

        default:
            #vedi getter_functions.php
            #function get_public_result($table,$pattern,$connected_db,$offset,$limit=3,$suggestion=false)
            $res=get_public_result($_GET['table'],$_GET['pattern'],$connected_db,0,6,true);
            break;
        }
    if(!$res||$res->num_rows==0)
        echo "<li class='entry_sug'>Nessun suggerimento</li>";
    else{
        while($row=$res->fetch_assoc())
            echo "<li class='entry_sug' onclick=\"autocomp(this.innerHTML)\" onclick=\"suggestion_search()\">".$row[$mapping[$_GET['table']]]."</li>";
    }
 
?>