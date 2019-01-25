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
	<title>MUY | Ultimi Upload</title>
    
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
        #vedi getter_functions.php
        #function get_today_friends_content($who,$connected,$offset)
        $res=get_today_friends_content($_SESSION['email'],$connected_db,0);
        if(!$res){
            $_GET['error']="Errore nella connessione con il server";
        }

    ?>

        <main>
            <div class="content">
                <?php
                    if(isset($_GET["error"])){
                        echo "<span class='error_span'>".$_GET["error"]."</span>";
                    }
                    if(isset($_GET["msg"])){
                        echo "<span class='message_span'>".$_GET["msg"]."</span>";
                    }
                ?>
                    <ul class='search_results'>
                        <?php
                            if($res->num_rows==0)
                                echo "<div class='error_div'><span class='message_span'>La ricerca non ha prodotto risultati</span></div>";
                                #stampo la tabella,vedi la funzione in display functions
                            display_refreshing_block('todayContent','',$res,1);
                        ?>
                    </ul>
            </div>
        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>
</body>

</html>