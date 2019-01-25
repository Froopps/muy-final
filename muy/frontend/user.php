<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
?>

<!DOCTYPE HTML>
<html>

<head>
    <?php
        $redirect_with_error="Location:$location/frontend/home.php?error=";
        if($error_connection["flag"]){
            $redirect_with_error.=urlencode($error_connection["msg"]);
            header($redirect_with_error);
            exit();
        }
        $res=get_user_by_email($_GET["user"],$connected_db);
        $row=$res->fetch_assoc();
        if(!$res||$row['COUNT(*)']<=0){
            $redirect_with_error.=urlencode("Errore nella connessione con il database ");
            header($redirect_with_error);
            exit();
        }
        
        echo "<title>MUY | ".$row["nickname"]."</title>";
        include "../common/head.php";
    ?>
</head>

<body>

        <!-- controllo loggato -->
        <?php 
            if(isset($_SESSION["email"])){
                include "../common/header_logged.php";
                include "../common/sidebar_logged.php";
            }else{
                include "../common/header_unlogged.php";
                include "../common/sidebar_unlogged.html";
            }

            #controllo se è canale dell'utente loggato e eventualmente l'amicizia
            $self=false;
            if(isset($_SESSION["email"])&&$_SESSION["email"]==$_GET["user"])
                $self=true;
            $relationship="a";
            if(!$self)
                $relationship=get_relationship($_SESSION["email"],$_GET["user"],$connected_db);
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
                
                <div id="testa-user" class="flex-space-between">
                    <?php
                        display_user_info($row,$connected_db);
                        if($self){
                            echo "<div class=\"flex-col\">";
                            echo "<div><button class=\"in_notext\" type=\"button\" onclick=\"document.getElementById('modal_bg_2').style.display='flex'\">Nuovo canale</button></div>";
                            echo "<div><form action=\"../backend/delete_user.php\" method=\"get\" onsubmit=\"return confirm('Conferma eliminazione')\"><button class=\"in_notext\" id=\"delete\" type=\"submit\">Elimina utente</button></form></div>";
                            echo "</div>";
                        }
                    ?>
                </div>
                
                <?php
                    $res=get_channel_by_owner($_GET["user"],$connected_db);
                    if(!$res){
                        $redirect_with_error.=urlencode("Errore nella connessione con il database");
                        log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                        //header($redirect_with_error);
                        $connected_db->close();
                        exit();
                    }
                    $no_channel=1;
                    $no_content=1;
                    while($row=$res->fetch_assoc()){
                        if($self||$row["visibilita"]!="private"){
                            $no_channel=0;
                            echo "<div class=\"categoria\">";
                                echo "<div class=\"categoria_user_nome\">";
                                    if($self||$row["visibilita"]=="public")
                                        echo "<a class=\"categoria_titolo\" href=\"canale.php?nome=".htmlentities(urlencode($row["nome"]))."&proprietario=".htmlentities(urlencode($_GET["user"]))."\">".stripslashes($row["nome"])."</a>";
                                    else
                                        echo "<h2>".stripslashes($row["nome"])."</h2>";
                                    if($self){
                                        echo "<div class=\"flex-center\">";
                                            echo "<button class=\"delete_button\" onclick=\"delete_channel(this,'".$row["nome"]."','".$row["proprietario"]."')\"></button>";
                                            echo "<a class=\"glyph-button\" href=\"upload.php?canale=".htmlentities(urlencode($row["nome"]))."\"><img src=\"../sources/images/plus.png\" width=\"30px\" alt=\"Aggiungi\"></a>";
                                        echo "</div>";
                                    }
                                echo "</div>";
                                echo "<hr align=\"left\">";
                                echo "<div class=\"eticanale\">";
                                echo "<div class='can-vis'>#".toUpperFirst($row["visibilita"])."</div>";
                                if(!empty($row["etichetta"])){
                                        $eti=explode(",",$row["etichetta"]);
                                        foreach($eti as $et){
                                            echo "<a id='no_link' class='etichetta'>#".stripslashes($et)."</a>";
                                        }
                                }
                                echo "</div>";
                                echo "<div class=\"scrollbar\">";
                                    if($self||$row["visibilita"]=="public"||$relationship=="a"){
                                        $query="SELECT * FROM oggettoMultimediale WHERE canale='".escape($row["nome"],$connected_db)."' AND proprietario='".escape($row["proprietario"],$connected_db)."' ORDER BY `dataCaricamento` DESC";
                                        $res_ogg=$connected_db->query($query);
                                        if(!$res_ogg){
                                            
                                            log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                                            echo "<span class='error_span>Errore nella connessione col server</span>";
                                            exit();
                                        }
                                        $no_content=1;
                                        while($row_ogg=$res_ogg->fetch_assoc()){
                                            $no_content=0;
                                            if($self)
                                                echo "<span class=\"obj_mod\">";
                                            display_multimedia_object($row_ogg,$connected_db);
                                            if($self){
                                                echo "<div class=\"obj_mod_button\"><button class=\"delete_button\" onclick=\"delete_content(this,'".$row_ogg["extID"]."')\"></button></div>";
                                                echo "</span>";
                                            }
                                        }
                                        if($no_content)
                                            echo "<span class='message_span'>Non c'è nessun elemento da mostrare</span>";
                                    }else
                                        echo "<span class='message_span'>Questo canale è social</span>";
                                echo "</div>";
                            echo "</div>";
                        }
                    }
                    if($no_channel)
                        echo "<span id='no-ch' class='message_span'>Non c'è nessun canale da mostrare</span>";
                    $connected_db->close();
                ?>
                
            </div>

        </main>

    <script type="text/javascript" src="../common/script/setup.js"></script>
    <script type="text/javascript" src="../common/script/_aux.js"></script>
    <script type="text/javascript" src="../common/script/friendship.js"></script>
    <script type="text/javascript" src="../common/script/search.js"></script>

</body>

</html>