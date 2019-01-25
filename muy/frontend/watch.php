<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    $redirect_with_error="Location: home.php?error=";
    if($error_connection["flag"]){
        $redirect_with_error.=urlencode($error_connection["msg"]);
        header($redirect_with_error);
        exit();
    }
    if(isset($_GET["id"])){
        $res=get_content_by_id($_GET["id"],$connected_db);
        if(!$res||$res->num_rows!=1)
            header($redirect_with_error.urlencode("Errore nella visualizzazione del contenuto"));
        if($error_connection["flag"])
            header($redirect_with_error.urlencode("Errore nella visualizzazione del contenuto"));
        $row=$res->fetch_assoc();
    }else
        header($redirect_with_error.urlencode("Errore nella visualizzazione del contenuto"));
    #controllo se contenuto è dell'utente loggato
    $self=false;
    if(isset($_SESSION["email"])&&$_SESSION["email"]==$row["proprietario"])
        $self=true;
    #controlli amicizia e visibilità
    #see getter functions
    $vis=get_channel_visibility($row["canale"],$row["proprietario"],$connected_db);
    if(!$self){
        if($vis=="private")
            header($redirect_with_error.urlencode("Accesso negato"));
        else if($vis=="social"){
            if(isset($_SESSION["email"])){
                $relationship=get_relationship($_SESSION["email"],$row["proprietario"],$connected_db);
                if($relationship!="a")
                    header($redirect_with_error.urlencode("Accesso negato"));
            }else
                header($redirect_with_error.urlencode("Accesso negato"));
        }
    }
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | <?php echo $row["titolo"]; ?></title>
    
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

                <div class="show-obj">
                    <div class="show">
                        <?php

                            //questo fa schifo nello script dell'api
                            $ytid="";
                        
                            if($row["percorso"][0]!="/"){
                                //video yt
                                echo "<div id=\"yt_player\"></div>";
                                $ytid=getYoutubeId($row["percorso"]);
                            }else{
                                $file=explode("/",$row["percorso"]);
                                $ext=explode(".",$file[count($file)-1]);
                                $ext=$ext[count($ext)-1];

                                //$_SERVER["DOCUMENT_ROOT"]."/muy/sources/content".$ytid;
                                switch($row["tipo"]){
                                    case "a":
                                        $file=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res".$row["anteprima"];
                                        $image="data:image/$ext;base64,".base64_encode(file_get_contents($file));
                                        echo "<div class=\"audio-cover\"><img class=\"oggetto\" src=\"".$image."\" onclick=\"document.getElementById('modal_bg_img').style.display='flex'\"></div>";
                                        echo "<div class=\"audio-ctrl-bar\"><audio controls='controls'>";
                                        echo "<source src=\"../muy_res".$row["percorso"]."\" type=\"audio/".$ext."\">Your browser does not support the audio element.</audio></div>";
                                        break;
                                    case "v":
                                        echo "<video width=\"100%\" height=\"100%\" controls>";
                                        echo "<source src=\"../muy_res".$row["percorso"]."\" type=\"video/".$ext."\">Your browser does not support HTML5 video.</video>";
                                        break;
                                    case "i":
                                        $file=$_SERVER["DOCUMENT_ROOT"]."\muy\muy_res".$row["percorso"];
                                        $image="data:image/$ext;base64,".base64_encode(file_get_contents($file));
                                        echo "<img class=\"oggetto\" src=\"".$image."\" onclick=\"document.getElementById('modal_bg_img').style.display='flex'\">";
                                        break;
                                    default:
                                        break;
                                }
                            }
                        ?>
                    </div>
                    <div class="infobox">
                        <div class="info-head">
                            <div class="info-titolo"><h1><?php echo $row["titolo"]; ?></h1></div>
                                <div class="ilikeit-box">

                                    <?php
                                        if(isset($_SESSION['email'])){
                                            $query="SELECT voto FROM oggettoMultimediale JOIN valutazione WHERE utente='".escape($_SESSION['email'],$connected_db)."' AND relativoA= ALL(SELECT percorso FROM oggettoMultimediale WHERE extID='".$row['extID']."')";
                                            $res=$connected_db->query($query);
                                            if(!$res){
                                                log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                                                echo "<span class='error_span>Errore nella connessione col server</span>";
                                                exit();
                                            }
                                            $like=$res->fetch_row()[0];
                                            for($i=1;$i<=$like;$i++)
                                                echo "<button class=\"ili\" style=\"background-image:url(../sources/images/star-f.png)\" type=\"button\" onclick=\"like_it('".$row['extID']."',this.value)\" value='".$i."'></button>";
                                            for($i=$like+1;$i<=5;$i++)
                                                echo "<button class=\"ili\" style=\"background-image:url(../sources/images/star-e.png)\" type=\"button\" onclick=\"like_it('".$row['extID']."',this.value)\" value='".$i."'></button>";
                                            echo "<button class=\"ili\" style=\"background-image:url(../sources/images/star-r.png)\" type=\"button\" onclick=\"like_it('".$row['extID']."',this.value)\" value='0'></button>";
                                        } 
                                    ?>
                                </div>
                                <div><h1 id="visual">
                                
                                <?php echo $row["visualizzazioni"]; ?>
                            
                            </h1></div>
                        </div>
                        <?php
                            $nome=get_user_by_email($row["proprietario"],$connected_db);
                            echo "<div class=\"info-head\"><a class=\"oggetto-canale\" href=\"user.php?user=".$row["proprietario"]."\">".$nome->fetch_assoc()["nickname"]."</a><a class=\"oggetto-canale\" href=\"canale.php?nome=".$row["canale"]."&proprietario=".$row["proprietario"]."\">".$row["canale"]."</a></div>";
                        
                            $res=get_content_tag($row["percorso"],$connected_db);
                            echo "<div class=\"eticanale\" id=\"info-eti\">";
                            if($res->num_rows>0){
                                while($row_tag=$res->fetch_assoc()){
                                    echo "<span class=\"eti-span\"><a class=\"etichetta eli\" href=\"categoria.php?tag=".htmlentities(urlencode(stripslashes($row_tag["tag"])))."\">".stripslashes($row_tag["tag"])."</a>";
                                    if($self)
                                        echo "<button class=\"cross-but\" type=\"button\" onclick=\"del_eti('".escape($row_tag["tag"],$connected_db)."','".$_GET["id"]."',this)\">x</button>";
                                    echo "</span>";
                                }
                            }
                            if($self){
                                echo "<input type=\"hidden\" name=\"newtag\" placeholder=\"#...\"/>";
                                echo "<button class=\"add_button\" type=\"button\" onclick=\"add_eti('".$_GET["id"]."',this)\"></button>";
                                echo "<button class=\"cross_button\" type=\"button\" style=\"display: none\" onclick=\"close_eti(this)\"></button>";
                            }
                            echo "</div>";
                        if(!empty($row["descrizione"]))
                            echo "<div class=\"info-descrizione\">".$row["descrizione"]."</div>";
                        else
                            echo "<div class=\"info-descrizione\">Nessuna descrizione</div>";
                        ?>
                    </div>
                </div>
                <div class="commentbox">
                    <div id="lista-commenti">
                        <?php
                            $query="SELECT * FROM commento JOIN utente ON commento.utente=utente.email WHERE contenuto='".escape($row["percorso"],$connected_db)."' ORDER BY dataRilascio ASC";
                            $res=$connected_db->query($query);
                            if($res->num_rows>0){
                                while($row=$res->fetch_assoc()){
                                    echo "<div class=\"commento\">";
                                        echo "<div class=\"comm-head\">";
                                            echo "<div class=\"flex-center\">";
                                                $pro_pic="data:image/png;base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/muy/muy_res/".$row["foto"]));
                                                echo "<a href=\"user.php?user=".$row["email"]."\"><img class=\"comm-img\" src=\"".$pro_pic."\"></a>";
                                                echo "<a class=\"comm-aut\" href=\"user.php?user=".$row["email"]."\"><b>".$row["nickname"]."</b></a>";
                                            echo "</div>";
                                            echo "<div>";
                                            if($_SESSION["email"]==$row["email"])
                                                echo "<button class=\"delete-cross\" type=\"button\" onclick=\"delete_comment('".$row["id"]."','".escape($row["utente"],$connected_db)."',this)\">x</button>";
                                            echo "</div>";
                                        echo "</div>";
                                        echo "<div class=\"comm-text\">".$row["testo"]."</div>";
                                    echo "</div>";
                                }
                            }else
                                echo "<div id=\"no-comment\">Nessun commento</div>";
                        ?>                        
                    </div>
                    <div class="input-comment">
                        <textarea class="in_comment" id="comm" rows="3" placeholder="Commenta..."></textarea>
                        <?php
                            if(isset($_SESSION["nome"])){
                                $query="SELECT foto FROM utente WHERE email='".escape($_SESSION["email"],$connected_db)."'";
                                $res=$connected_db->query($query);
                                $row=$res->fetch_assoc();
                                $pro_pic="data:image/png;base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/muy/muy_res/".$row["foto"]));
                                echo "<div class=\"com-button\"><button class=\"in_notext\" type=\"button\" onclick=\"comment(document.getElementById('comm'),'".$_GET['id']."',document.getElementById('lista-commenti'),'".escape($_SESSION['nome'],$connected_db)."','".$pro_pic."','".escape($_SESSION['email'],$connected_db)."')\">Commenta</button></div>";
                            }else
                                echo "<div class=\"com-button\"><button class=\"in_notext\" type=\"button\" onclick=\"comment(document.getElementById('comm'),'".$_GET['id']."',document.getElementById('lista-commenti'),'no','no')\">Commenta</button></div>";
                            $connected_db->close();
                        ?>
                    </div>
                </div>
            </div>
        </main>
    
    <div id="modal_bg_img" class="modal_bg">
        <?php echo "<img class=\"oggetto\" src=\"".$image."\">"; ?>
    </div>

    <script>
        var el=document.getElementById("modal_bg_img")
        el.onclick=function(event){
                el.style.display='none'
        }
        
        function close_eti(button){
            button.style.display = "none"
            document.getElementsByName("newtag")[0].value = ""
            document.getElementsByName("newtag")[0].type = "hidden"
        }
        
        
        window.onload = function(){visual(<?php echo $_GET["id"]; ?>)}
    </script>
    <script type="text/javascript" src="../common/script/setup.js"></script>
    <script type="text/javascript" src="../common/script/_aux.js"></script>
    <script type="text/javascript" src="../common/script/search.js"></script>
    <script>
        
        // This code loads the IFrame Player API code asynchronously.
        var tag = document.createElement('script');

        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // This function creates an <iframe> (and YouTube player) after the API code downloads.
        var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('yt_player', {
            height: '1080',
            width: '1920',
            videoId: '<?php echo $ytid; ?>',
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
        }

        // The API will call this function when the video player is ready.
        function onPlayerReady(event) {
            event.target.playVideo();
        }

        // The API calls this function when the player's state changes.
        var done = false;
        function onPlayerStateChange(event) {
            console.log("youtube-ok")
        }
        function stopVideo() {
            player.stopVideo();
        }

    </script>

</body>

</html>