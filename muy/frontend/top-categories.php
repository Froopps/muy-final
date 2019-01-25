<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | Top categories</title>
    
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
            $top=get_top_tags($connected_db);
            if(!isset($top))
                $_GET['error']='Errore nella connessione col server';
        ?>

        <main>
            <div class="content">
                <?php
                    if(isset($_GET["error"])){
                        echo "<span class='error_span'>".$_GET["error"]."</span>";
                        if(!($top))
                            exit();
                    }
                    if(isset($_GET["msg"])){
                        echo "<span class='message_span'>".$_GET["msg"]."</span>";
                    }
                ?>
                <a class='categoria_titolo' href='../common/get_xml_top_usr.php?action=tags'>Esporta in xml</a>;
                <table id="classifica_cat">
                    <?php
                        $res=get_top_tags($connected_db);
                        if(!$res)
                            echo "<span class='error_span'>Errore nella connessione al server</span>";
                        else{
                            $b=true;
                            if($res->num_rows==0){
                                echo "<span class='message_span'>Non ci sono etichette top al momento</span>";
                                $b=false;
                            }
                            $count=0;
                            for($i=0;$i<5&&$b;$i++){
                                echo "<tr>";
                                for($j=0;$j<2;$j++){
                                    $count++;
                                    if(!$row=$res->fetch_assoc()){
                                        $b=false;
                                        break;
                                    }
                                    echo "<td class=\"eti_position\"><h1>#$count</h1></td>";
                                    echo "<td class=\"tab_top_cat\">";
                                    display_tag_mosaic($row["tag"],$connected_db);
                                    echo "</td>";
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