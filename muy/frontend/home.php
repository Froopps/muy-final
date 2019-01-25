<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | Home</title>
    
    <?php 
        include "../common/head.php";
        if($error_connection["flag"])
            $_GET["error"]="Errore connessione";
    ?>
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
        $mvvideo=get_most_visited('v',$connected_db);
        $mvaudio=get_most_visited('a',$connected_db);
        $mvimages=get_most_visited('i',$connected_db);
        if(!(isset($mvvideo)&&isset($mvaudio)&&isset($mvimages)))
            $_GET['error']='Errore nella connessione col server';
    ?>

        <main>

            <div class="content">
                <?php
                    if(isset($_GET["error"])){

                        #edit span to achieve a fashion error displaying
                        echo "<span class='error_span'>".$_GET["error"]."</span>";
                        if(!($mvvideo&&$mvaudio&&$mvimages))
                            exit();
                    }

                    if(isset($_GET["msg"])){
                        #edit span to achieve a fashion message displaying
                        echo "<span class='message_span'>".$_GET["msg"]."</span>";
                    }
                ?>
                <div class="categoria">
                    <?php
                        echo "<div><h2>Most visited videos</h2></div>";
                    ?>
                    <div class="scrollbar">
                        <?php
                            if($mvvideo->num_rows==0)
                                echo "<span class='message_span'>Non ci sono ancora video</span>";
                            while($row=$mvvideo->fetch_assoc())
                                display_multimedia_object($row,$connected_db); 
                        ?>
                    </div>
                </div>
                <div class="categoria">
                    <?php
                        echo "<div><h2>Most visited audios</h2></div>"
                    ?>
                    <div class="scrollbar">
                        <?php
                            if($mvaudio->num_rows==0)
                                echo "<span class='message_span'>Non ci sono ancora audio</span>";
                            while($row=$mvaudio->fetch_assoc())
                                display_multimedia_object($row,$connected_db);
                        ?>
                    </div>
                </div>
                <div class="categoria">
                    <?php
                        echo "<div><h2>Most visited images</h2></div>"
                    ?>
                    <div class="scrollbar">
                    <?php
                        if($mvimages->num_rows==0)
                            echo "<span class='message_span'>Non ci sono ancora immagini</span>";
                        while($row=$mvimages->fetch_assoc())
                            display_multimedia_object($row,$connected_db);
                    ?>
                    </div>
                </div>
            </div>
        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>
</body>

</html>