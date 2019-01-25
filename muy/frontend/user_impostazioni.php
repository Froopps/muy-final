<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    $redirect_with_error="Location:$location/frontend/home.php?error=";
    if($error_connection["flag"]){
        $redirect_with_error.=urlencode($error_connection["msg"]);
        header($redirect_with_error);
        exit();
    }
    if(!isset($_SESSION["email"])){
        $rediret_with_error.=urlencode("Accesso negato");
        header($redirect_with_error);
        exit();
    }
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>MUY | Impostazioni</title>
    <link rel="stylesheet" href="../node_modules/croppie/croppie.css">
    
    <?php include "../common/head.php"; ?>
</head>

<body>
    <?php
        include "../common/header_logged.php";
        include "../common/sidebar_logged.php";
        include "../common/modal_pwd_change.php"
    ?>
    <main>
        <div class="content-centre">
            <?php
                if(isset($_GET["error"])){
                    echo "<span class='error_span'>".$_GET["error"]."</span>";
                }
                if(isset($_GET["msg"])){
                    echo "<span class='message_span'>".$_GET["msg"]."</span>";
                }
            ?>
            <div class="headingArea">
                <h2>Aggiorna</h2>
            </div>
            <div class="user_impo_container">
                <table class="signup-table impo_table">
                    <?php
                        
                        $res=get_user_by_email($_SESSION["email"],$connected_db);
                        if(!$res){
                            log_into("Errore di esecuzione della query".$query." ".$connected_db->error);
                            echo "<span class='error_span>Errore nella connessione col server</span>";
                            exit();
                        }
                        $row=$res->fetch_assoc();
                        //echo "<tr><td><input type='file' accept='image/png,image/jpeg' onchange=\"crop_image(this,document.getElementById('croppie_box'),'".$_SESSION["email"]."',document.getElementById('crop_button'))\"></td><td><button id='crop_button' class='in_notext' type='button'>Aggiorna</button></td><td><button type='button' class='in_notext' onclick=\"set_def_foto('".$_SESSION["email"]."',this)\">Elimina</button></td></tr>";
                    
                    ?>
                    <tr class='heading_in_table'><td><h4>Foto profilo</h4><td></tr>
                    <tr>
                        <td><input type='file' accept='image/*' onchange="crop_image(this,document.getElementById('croppie_box'),document.getElementById('crop_button'),'pro')"></td>
                        <td></td>
                        <td><button type='button' class='in_notext' onclick="set_def_foto('<?php echo $_SESSION['email'];?>',this)">Elimina</button></td>
                        <td><button id='crop_button' class='in_notext' type='button'>Aggiorna</button></td>
                    </tr>
                    <tr><td><img id='croppie_box' src='#' alt='Spiazènti'></td></tr>
                    <tr class='heading_in_table'><td><h4>Email</h4></td></tr>
                    <tr>
                        <td><input class='in_email_up' type="text" name='email' value="<?php echo escape($row['email'],$connected_db) ;?>" onkeyup="pattern_validation(this,0,'mail-y','mail-n','mail-ny')"></td>
                        <td>
                            <img id='mail-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                            <img id='mail-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                            <img id='mail-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                        </td>
                        <td></td>
                        <td><button class='in_notext' type='button' onclick="update_user_info(document.getElementsByClassName('in_email_up')[0],this)">Aggiorna</button></td>
                    </tr>
                    <tr class='heading_in_table'><td><h4>Password:</h4></td></tr>
                    <tr>
                        <td><input class='in_pass_up' type="password" name='passwd'></td>
                        <td>
                            <img id='mail-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                            <img id='mail-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                            <img id='mail-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                        </td>
                        <td></td>
                        <td><button id='pass_butt' class='in_notext' type='button' onclick="show_conf_def()">Aggiorna</button></td>
                    </tr>
                    <tr class='heading_in_table'><td><h4>Nome</h4></td></tr>
                    <tr>
                        <td><input class='in_nome_up' type="text" name='nome' value='<?php echo escape($row['nome'];?>' onkeyup="pattern_validation(this,1,'nom-y','nom-n','nom-ny')"></td>
                        <td>
                            <img id='nom-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                            <img id='nom-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                            <img id='nom-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                        </td>
                        <td></td>
                        <td><button class='in_notext' type='button' onclick="update_user_info(document.getElementsByClassName('in_nome_up')[0],this)">Aggiorna</button></td>
                    </tr>
                    <tr class='heading_in_table'><td><h4>Cognome</h4></td></tr>
                    <tr>
                        <td><input class='in_cognome_up' type="text" name='cognome' value='<?php echo $row['cognome'];?>' onkeyup="pattern_validation(this,1,'cog-y','cog-n','cog-ny')"></td>
                        <td>
                            <img id='cog-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                            <img id='cog-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                            <img id='cog-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                        </td>
                        <td></td>
                        <td><button class='in_notext' type='button' onclick="update_user_info(document.getElementsByClassName('in_cognome_up')[0],this)">Aggiorna</button></td>
                    </tr>
                    <tr class='heading_in_table'><td><h4>Nickname</h4><td></tr>
                    <tr>
                        <td><input class='in_nickname_up' type="text" name='nickname' value='<?php echo $row['nickname'];?>'></td>
                        <td></td>
                        <td></td>
                        <td><button class='in_notext' type='button' onclick="update_user_info(document.getElementsByClassName('in_nickname_up')[0],this)">Aggiorna</button></td>
                    </tr>
                    <tr class='heading_in_table'><td><h4>Sesso</h4><td></tr>
                    <tr>
                        <td>
                            <select class='in_sex_up' name="sesso" value=<?php echo $row['sesso'];?>>
                                <option value="Maschio">Male</option>
                                <option value="Femmina">Female</option>
                            </select>
                        </td>
                        <td></td>
                        <td></td>
                        <td><button class='in_notext' type='button' onclick="update_user_info(document.getElementsByClassName('in_sex_up')[0],this)">Aggiorna</button></td>
                    </tr>
                    <tr class='heading_in_table'><td><h4>Città</h4><td></tr>
                    <tr>
                        <td><input class='in_citta_up' type="text" name='citta' value='<?php echo $row['citta'];?>' onkeyup="pattern_validation(this,1,'cit-y','cit-n','cit-ny')"></td>
                        <td>
                            <img id='cit-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                            <img id='cit-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                            <img id='cit-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                        </td>
                        <td></td>
                        <td><button class='in_notext' type='button' onclick="update_user_info(document.getElementsByClassName('in_citta_up')[0],this)">Aggiorna</button></td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
    <script type="text/javascript" src="../common/script/setup.js"></script>
    <script type="text/javascript" src="../common/script/user_info_validation.js"></script>
    <script type="text/javascript" src="../common/script/_aux.js"></script>
    <script type="text/javascript" src="../node_modules/croppie/croppie.js"></script>
    <script type="text/javascript" src="../common/script/search.js"></script>
</body>
</html>

