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
	<title>MUY | Top users</title>
    
    <?php include "../common/head.php"; ?>
</head>

<body>
    
        <!-- controllo loggato -->
        <?php 
            if(isset($_SESSION["email"])){
                include "../common/header_logged.php";
                include "../common/sidebar_logged.php";
            }
            else{
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
                <a class='categoria_titolo' href='../common/get_xml_top_usr.php?action=user'>Esporta in xml</a>;
                <table id="classifica_usr">
                    
                    <?php
                        
                        $res=get_top_vip($connected_db);
                        if(!$res)
                            echo "<span class='error_span'>Errore nella connessione al server</span>";
                        else{
                            $b=true;
							$row=$res->fetch_assoc();
                            if($res->num_rows==0||!$row["email"]){
                                echo "<span class='message_span'>Non ci sono utenti top users al momento</span>";
                                $b=false;
                            }
                            for($i=0;$i<5&&$b;$i++){
                                echo "<tr>";
                                for($j=0;$j<2;$j++){
                                    if(!$row){
                                        $b=false;
                                        break;
                                    }
                                    echo "<td class='tab_top_usr'>";
                                    echo "<h2>Voti totali:".$row['somma_voti']."</h2>";
                                    display_user_info($row,$connected_db);
                                    echo "</td>";
									$row=$res->fetch_assoc();
                                }
                                echo "</tr>";
                            }
                        }
                    ?>
                    
                </table>
            </div>
        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>
</body>

</html>