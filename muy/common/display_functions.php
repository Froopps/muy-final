<?php
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    function display_user_info($info,$connected_db){
        $written_key=array("dataNascita"=>"compleanno","citta"=>"città","cittaNascita"=>"luogo di nascita");
        echo "<table class='user_info'><tr>";
        $pub=array('email');
        foreach(get_visible_list($info["visibilita"]) as $p)
            array_push($pub,$p);
        $topvip=isTopVip($info["email"],$connected_db);
        #change in /default/logo.jpg
        #can't link by url something outside webroot directory for security constraints. So we need to embedd
        #the URI urlencoding file_get_contents() return value
        $pro_pic=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res";
        $pro_pic_alt="-No image-";
        if(!file_exists($pro_pic."/".$info["foto"]))
            log_into("Can't find profile pic at ".$pro_pic."/".$info["foto"]);

        $pro_pic="data:image/png;base64,".base64_encode(file_get_contents($pro_pic."/".stripslashes($info["foto"])));
        if($topvip)
            echo "<td class='pic_and_but' rowspan='2'><a href='user.php?user=".htmlentities(urlencode(stripslashes($info["email"])))."'><img class='propic' id='top-propic' src=$pro_pic alt=$pro_pic_alt></a><div>";
        else
            echo "<td class='pic_and_but' rowspan='2'><a href='user.php?user=".htmlentities(urlencode(stripslashes($info["email"])))."'><img class='propic' src=$pro_pic alt=$pro_pic_alt></a><div>";
        if(isset($_SESSION["email"])&&$_SESSION["email"]!=$info["email"]){
            $status=get_relationship($_SESSION["email"],$info["email"],$connected_db);
            if(!$status){
                echo "<span class='error_span'>Errore nella connessione al server</span></div></td></tr></table>";
                exit();
            }
            echo "<button class='in_notext' type='button' ";
            switch($status){
                case 'a':
                    echo "id='disabled' disabled>Amico";
                    break;
                #restituito da get_relationship in caso di nessuna relazione ne presente ne passata
                case "no":
                    echo "onclick=\"request_fr(this,'".escape($info['email'],$connected_db)."')\">Invia richiesta";
                    break;
                case 'pending':
                    echo "id='disabled' disabled>In attesa di conferma";
                    break;
                default:
                    echo "id='disabled' style='background-color: #837d7d' disabled>Bloccato";
                    break;


            }
            
            echo "</button></div></td>";
        }
        
        echo "<td class='info'><a class='utente' href='user.php?user=".htmlentities(urlencode(stripslashes($info["email"])))."'>".$info["nickname"]."</a></td>";
        if($topvip){
            echo "<td><img class='top-vip_logo' src='../sources/images/top-vip.png'></td>";
            echo "<td><a class='utente' href='top-users.php'>#$topvip</a></td>";
        }
        echo "</tr><tr><td class='info'><ul>";
        foreach($pub as $key){
            if(isset($written_key[$key])){
                if(isset($info[$key]))
                    echo "<li>".toUpperFirst($written_key[$key]).": ".$info[$key]."</li>";
            }else if(isset($info[$key]))
                echo "<li>".toUpperFirst($key).": ".$info[$key]."</li>";
        }
        echo "</ul></td></tr></table>";
    }

    function display_multimedia_object($info,$connected_db){
        $path=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res";
        echo "<span class=\"obj_multimedia\">";
        #leva if e lascia solo else
        if($info["anteprima"]=="anteprima_yt")
            $cover="data:image/png;base64,".base64_encode(file_get_contents("../sources/images/cover.png"));
        else
            $cover="data:image/png;base64,".base64_encode(file_get_contents($path.$info["anteprima"]));
        echo "<a class=\"oggetto\" href=\"watch.php?id=".$info["extID"]."\"><img class=\"imgobj\" src=\"".$cover."\" alt=\"cover\"></a>";
        echo "<div class=\"ohidden\"><a class=\"oggetto-titolo\" href=\"watch.php?id=".$info["extID"]."\">".$info["titolo"]."</a></div>";
        $res=get_user_by_email($info["proprietario"],$connected_db);
        
        if(!$res){
            echo "<span class='error_span>Errore nella connessione col server</span>";
            exit();
        }
        $row=$res->fetch_assoc();
        echo "<div class=\"ohidden\"><a class=\"oggetto-canale\" href=\"user.php?user=".htmlentities(urlencode(stripslashes($info["proprietario"])))."\">".$row["nickname"]."</a></div>";
        echo "<div class=\"ohidden\"><a class=\"oggetto-canale\" href=\"canale.php?nome=".htmlentities(urlencode($info["canale"]))."&proprietario=".htmlentities(urlencode($info["proprietario"]))."\">".$info["canale"]."</a></div>";
        echo "<h3>Visual: ".$info["visualizzazioni"]."</h3>";
        echo "<h3 class=\"rate\">".valutazione($info["percorso"],$connected_db)."</h3>";
        echo "</span>";
    }

    function display_friendslist_rows($res,$next,$action,$connected_db){
        $no_more=false;
        for($j=0;$j<2&&!$no_more;$j++){
            echo "<div class='friend_list_row'>";
            for($i=0;$i<2;$i++){
                $row=$res->fetch_assoc();
                if(!$row){
                    $no_more=true;
                    break;
                }
                display_friendslist_entry($row,$action);
            }
            echo "</div>";
        }
        if(!$no_more){
            #e un bottone 'altro' dal cui valore dipenderà l'offset con cui fare la query per mostrare altri risultati
            echo "<div class='error_div'><span><button class='in_notext show_more' style='background-color:#837d7d' value='$next' type='button' onclick=\"refresh_friendslist('$action',this)\">Altro</button></span></div>";
        }

    }

    function display_friendslist_entry($info,$action){
        global $location;
        global $connected_db;
        $pro_pic=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res";
        $pro_pic_alt="Spiacenti foto non trovata";
        if(!file_exists($pro_pic."/".stripslashes($info["foto"])))
            log_into("Can't find profile pic at ".$pro_pic."/".$info["foto"]);
        $pro_pic="data:image/png;base64,".base64_encode(file_get_contents($pro_pic."/".stripslashes($info["foto"])));

        echo "<div class='friend_list_entry'>";
            echo "<div class='friend_list_entry_half'>";
                echo "<a href='$location/frontend/user.php?user=".htmlentities(urlencode(stripslashes($info['email'])))."'><img class='pro_pic_in_list' src='$pro_pic' alt=$pro_pic_alt></a>";
            echo "</div>";
            echo "<div class='friend_list_entry_half'>";
                echo "<div><a class='nick_in_link' href='$location/frontend/user.php?user=".htmlentities(urlencode(stripslashes($info['email'])))."'>".$info['nickname']."</a></div>";
                echo "<div class='action_div'>";
                switch($action){
                    case 'pending':
                        echo "<button class='in_notext' type='button' onclick=\"up_status('accept','".escape($info['email'],$connected_db)."',this)\">Conferma</button>";
                        echo "<button class='in_notext' style='background-color: #837d7d' type='button' onclick=\"up_status('deny','".escape($info['email'],$connected_db)."',this,'".escape($info['nickname'],$connected_db)."')\">Rifiuta</button>";
                        break;
                    
                    case 'friends':
                        echo "<button class='in_notext' style='background-color: #837d7d' type='button' onclick=\"up_status('erase','".escape($info['email'],$connected_db)."',this,'".escape($info['nickname'],$connected_db)."')\">Elimina</button>";
                        break;
                    case 'suggest':
                        echo "<button class='in_notext' type='button' onclick=\"request_fr(this,'".escape($info['email'],$connected_db)."')\">Invia richiesta";
                        break;
                }
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }

    function display_tag_mosaic($tag){
        global $connected_db;
        if($tag[0]!="#")
            $tag="#".$tag;
        $query="SELECT anteprima FROM `contenutoTaggato` JOIN `oggettoMultimediale` ON oggetto=percorso WHERE tag='".escape($tag,$connected_db)."' AND anteprima!='/defaults/default-audio.png' AND anteprima!='/defaults/default-image.png' ORDER BY RAND()";
        $res=$connected_db->query($query);
        if(!$res){
            echo "<span class='error_span>Errore nella connessione col server</span>";
            exit();
        }
        
        echo "<a class=\"mosaico\" id=\"bottom-layer\" href=\"categoria.php?tag=".htmlentities(urlencode(stripslashes($tag)))."\">";
            if($res->num_rows>3){
                for($i=0;$i<4;$i++){
                    $row=$res->fetch_assoc();
                    echo "<span class=\"mos_cel\"><img class=\"mos_img\" src=\"data:image/png;base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/muy/muy_res".escape($row["anteprima"],$connected_db)))."\"></span>";
                }
            }else{
                for($i=0;$i<$res->num_rows;$i++){
                    $row=$res->fetch_assoc();
                    echo "<span class=\"mos_cel\"><img class=\"mos_img\" src=\"data:image/png;base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/muy/muy_res".escape($row["anteprima"])))."\"></span>";
                }
                for($i=0;$i<4-$res->num_rows;$i++)
                    echo "<span class=\"mos_cel\"><img class=\"mos_img\" src=\"data:image/png;base64,".base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/muy/muy_res/defaults/default-audio.png"))."\"></span>";
            }
            echo "<table class=\"mosaico\" id=\"top-layer\" href=\"categoria.php?tag=".htmlentities(urlencode(stripslashes($tag)))."\">";
                echo "<tr><td><a class=\"etichetta\" href=\"categoria.php?tag=".htmlentities(urlencode(stripslashes($tag)))."\">".$tag."</a></td></tr>";
            echo "</table>";
        echo "</a>";
    }

    #logica simile a quella utilizzata per il refreshing della lista degli amici, ma questo stampa
    #stampa un blocco della pagina delle ricerche o dei video caricati nell'ultima giornata
    #query si riferisce al tipo della ricerca (canale,utente, ecc...)
    function display_refreshing_block($query,$pattern,$res,$next){
        #mapping è un array indicizzato utile a costruire la vista con  i link nella pagina dei risultati
        #delle ricerche a seconda del fatto che sia cercato un canale, un utente, un oggetto multimediale
        #o una categoria. A ogni selettore corrisponde un array che contiene valori per selezionare, dato
        # il risultato di una query $res, valori per il la foto, il testo del link, i nomi dei parametri della
        #get per costruire il link, i valori di tali parametri e due campi per costruire le informazioni
        #aggiuntive presenti nella vista
        $mapping=array("utente"=>array("foto","nickname","user.php?",array("user"),array("email"),"email","Account: "),
                        "canale"=>array("foto","nome","canale.php?",array("nome","proprietario"),array("nome","proprietario"),"proprietario","Proprietario: "),
                        "oggettoMultimediale"=>array("anteprima","titolo","watch.php?",array("id"),array("extID"),"tipo","Tipologia: "),
                        #utilizzata la stessa logica anche per fare il refresh della lista dei contenuti caricati nell'ultima giornata
                        #vedi ultima_giornata.php
                        "todayContent"=>array("anteprima","titolo","watch.php?",array("id"),array("extID"),"tipo","Tipologia: "),
                        "categoria"=>array("special","tag","categoria.php?",array("tag"),array("tag"),"data","",""),
                        "nearFriends"=>array("foto","nickname","user.php?",array("user"),array("email"),"email","Account: "),
        );
        $content_type=array("v"=>"Video","a"=>"Audio","i"=>"Immagine");
        $no_more=false;
        for($i=0;$i<3;$i++){
            $row=$res->fetch_assoc();
            if(!$row){
                $no_more=true;
                break;
            }
            $link_id="";
            for($j=0;$j<count($mapping[$query][3]);$j++)
                $link_id.=$mapping[$query][3][$j]."=".urlencode($row[$mapping[$query][4][$j]])."&";
            $link_id=substr($link_id,0,strlen($link_id)-1);
            if($query!='categoria'){
                if($query=='oggettoMultimediale')
                    $value=$content_type[$row[$mapping[$query][5]]];
                else
                    $value=$row[$mapping[$query][5]];
                $foto=$row[$mapping[$query][0]];
            }
            else{
                $foto=$mapping[$query][0];
                $value='';
            }
            $side_info=$mapping[$query][6].$value;
            display_search_entry($foto,$row[$mapping[$query][1]],$mapping[$query][2],$link_id,$side_info);
        }
        if(!$no_more){
            #e un bottone 'altro' dal cui valore dipenderà l'offset con cui fare la query per mostrare altri risultati
            echo "<div class='error_div'><span><button class='in_notext show_more' style='background-color:#837d7d' value='$next' type='button' onclick=\"refresh_search_res('$query',this,'$pattern')\">Altro</button></span></div>";
        }
    }
    
    #$link_id=parametri get della pagina
    function display_search_entry($foto,$link_text,$link_page,$link_id,$side_info){
        if($link_page=='watch.php?')
            $img_class="imgobj";
        else
            $img_class="propic propic_src";
        $link_page.=$link_id;
        $pro_pic=$_SERVER["DOCUMENT_ROOT"]."/muy/muy_res";
        $pro_pic_alt="Spiacenti foto non trovata";
        #se l'utente ha cercato una categoria stampare il mosaico
        if(!file_exists(stripslashes($pro_pic."/".$foto))){
            log_into("Can't find profile pic at ".$pro_pic."/".$foto);
            $pro_pic='';
        }
        else
            $pro_pic="data:image/png;base64,".base64_encode(file_get_contents($pro_pic."/".stripslashes($foto)));

        echo "<li class='search_results_entry'>";
            echo "<div class='search_foto'>";
            #se non è la categoria
            if($side_info!='')
                echo "<a href='$link_page'><img class='$img_class' src='$pro_pic' alt='Spiacenti contenuto non disponibile'></a>";
            else
                display_tag_mosaic($link_text);
            echo "</div>";
            echo "<div class='search_info'>";
                echo "<a class='categoria_titolo' href='$link_page'>$link_text</a>";
                echo "<p>$side_info</p>";
            echo "</div>";
        echo "</li>";
    }
?>