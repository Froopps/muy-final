<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    $redirect_with_error="Location:$location/frontend/etichette.php?error=";
        if($error_connection["flag"]){
            $redirect_with_error.=urlencode($error_connection["msg"]);
            header($redirect_with_error);
            exit();
        }
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | Etichette</title>
    
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
                <?php
                    $query="SELECT tag FROM categoria WHERE 1 ORDER BY tag ASC";
                    $res=$connected_db->query($query);
                    if(!$res){
                        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                        echo "<span class='error_span>Errore nella connessione col server</span>";
                        exit();
                    }
                    while($row=$res->fetch_assoc()){
                        $tag[]=$row["tag"];
                    }
                    $current_letter="";
                    if(empty($tag)){
                        echo "<span class='message_span'>Non ci sono ancora etichette</span>";
                        exit();
                    }
                    foreach($tag as $eti){
                        $letter=$eti[1];
                        if($current_letter==""){
                            echo "<h2>".strtoupper($letter)."</h2><hr class='short_hr'>";
                            $current_letter=$letter;
                        }else if($letter!=$current_letter){
                            echo "<div class='spazio-vert'></div><h2>".strtoupper($letter)."</h2><hr class='short_hr'>";
                            $current_letter=$letter;
                        }
                        echo "<a class='oggetto-canale' href=\"categoria.php?tag=".htmlentities(urlencode($eti))."\">".$eti."</a><br>";
                    }
                    $connected_db->close();
                ?>
            </div>

        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>
</body>

</html>