<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | <?php echo $tag=$_GET["tag"]; ?></title>
    
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
                <div class="categoria">
                <h2><?php echo $tag; ?></h2>
                <div>
                    <?php
                        if($error_connection["flag"]){
                            echo "<span class='error_span'>Errore nella connessione col server</span>";
                            exit();
                        }
                        if(isset($_SESSION["email"])){
                            $query="SELECT oggettoMultimediale.proprietario, canale.visibilita, oggettoMultimediale.extID, oggettoMultimediale.percorso, oggettoMultimediale.anteprima, oggettoMultimediale.titolo, oggettoMultimediale.visualizzazioni, oggettoMultimediale.canale FROM contenutoTaggato JOIN oggettoMultimediale ON contenutoTaggato.oggetto=oggettoMultimediale.percorso JOIN canale ON (oggettoMultimediale.proprietario=canale.proprietario AND oggettoMultimediale.canale=canale.nome) WHERE tag='".escape($tag,$connected_db)."'";
                            $relationship="";
                        }else
                            $query="SELECT * FROM contenutoTaggato JOIN oggettoMultimediale ON contenutoTaggato.oggetto=oggettoMultimediale.percorso JOIN canale ON (oggettoMultimediale.proprietario=canale.proprietario AND oggettoMultimediale.canale=canale.nome) WHERE tag='".escape($tag,$connected_db)."' AND visibilita='public'";
                        $res=$connected_db->query($query);
                        if(!$res){
                            log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                            echo "<span class='error_span>Errore nella connessione col server</span>";
                            exit();
                        }
                        if($res->num_rows>0){
                            while($row=$res->fetch_assoc()){
                                if(isset($_SESSION["email"])){
                                    if($_SESSION["email"]==$row["proprietario"])
                                        display_multimedia_object($row,$connected_db);
                                    else{
                                        if($row["visibilita"]=="public")
                                            display_multimedia_object($row,$connected_db);
                                        else if($row["visibilita"]=="social"){
                                            $relationship=get_relationship($_SESSION["email"],$row["proprietario"],$connected_db);
                                            if($relationship=="a")
                                                display_multimedia_object($row,$connected_db);
                                        }
                                    }
                                }else
                                    display_multimedia_object($row,$connected_db);
                            }
                        }else
                            echo "<span class='error_span'>Non puoi vedere nessun contenuto con questo tag</span>";
                    ?>
                </div>
                </div>
            </div>
        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>
</body>

</html>