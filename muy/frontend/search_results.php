<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    $redirect_with_error="Location:$location/frontend/home.php?error=";
    if($error_connection["flag"]){
        $redirect_with_error.=urlencode($error_connection["msg"]);
        header($redirect_with_error);
        exit();
    }
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | Ricerca</title>
    
    <?php include "../common/head.php"; ?>
</head>

<body>

    <?php 
        if(isset($_SESSION["email"])){
            include "../common/header_logged.php";
            include "../common/sidebar_logged.php";
        }else{
            include "../common/header_unlogged.php";
            include "../common/sidebar_unlogged.html";
        }

        if(!isset($_GET['src_type'])||!isset($_GET['src_txt']))
            $_GET['error']='Nessun parametro per la ricerca';
        
        else{
            switch($_GET['src_type']){

                case 'oggettoMultimediale':
                    if(!isset($_SESSION['email']))
                    $res=get_searched_content('',$_GET['src_txt'],$connected_db,0);
                    else
                        $res=get_searched_content(escape($_SESSION['email'],$connected_db),$_GET['src_txt'],$connected_db,0);
                    break;

                case 'canale':
                    if(!isset($_SESSION['email']))
                        $res=get_searched_channel('',$_GET['src_txt'],$connected_db,0);
                    else
                        $res=get_searched_channel(escape($_SESSION['email'],$connected_db),$_GET['src_txt'],$connected_db,0);
                    break;

                default:
                    $res=get_public_result($_GET['src_type'],$_GET['src_txt'],$connected_db,0);
                    break;
            }
            if(!$res){
                $_GET['error']="Errore nella connessione con il server";
            }
        }
    ?>

        <main class='search_page'>
            <div class="content">
                <?php
                    if(isset($_GET["error"])){
                        echo "<span class='error_span'>".$_GET["error"]."</span>";
                        exit();
                    }
                    if(isset($_GET["msg"])){
                        echo "<span class='message_span'>".$_GET["msg"]."</span>";
                    }
                ?>
                    <ul class='search_results'>
                        <?php
                            if($res->num_rows==0)
                                echo "<div class='error_div'><span class='message_span'>La ricerca non ha prodotto risultati</span></div>";
                                #stampo la tabella,vedi la funzione in display functions 1 è il moltiplicatore dell'offset
                                #per la query successiva
                            display_refreshing_block($_GET['src_type'],$_GET['src_txt'],$res,1);
                        ?>
                    </ul>
            </div>
        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>
</body>

</html>