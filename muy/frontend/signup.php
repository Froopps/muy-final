<?php
    session_start();
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | Registrazione</title>
	
    <?php include "../common/head.php"; ?>
</head>

<body>

        <?php
            include "../common/header_unlogged.php";
            include "../common/sidebar_unlogged.html";
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
                <form id="sign_up_form" enctype="multipart/form-data" action="../backend/signup.php" method="post">
                    <table id="signup-table">
                        <tr>
                            <th colspan="4">Benvenuto su MUY, registrati</th>
                        </tr>
                        <tr>
                            <td colspan="4">e accedi a un mondo di contenuti incredibili</td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td>Privato</td>
                        </tr>
                        <tr>
                            <td>e-Mail*:</td>
                            <td><input type="text" name="mail" placeholder="e-mail" required onkeyup="pattern_validation(this,0,'mail-y','mail-n','mail-ny')"></td>
                            <td>
                                <img id='mail-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='mail-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='mail-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                        </tr>
                        <tr>
                            <td>Password*:</td>
                            <td><input id="pwd_in_signup" type="password" name="pwd" required onchange="confirm_check('pwd_conf','pwdc-y','pwdc-n','pwdc-ny')" onkeyup="pass_val(this,'pwd-y','pwd-n','pwd-ny')"></td>
                            <td>
                                <img id='pwd-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='pwd-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='pwd-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                        </tr>
                        <tr>
                            <td>Conferma Password*:</td>
                            <td><input id="pwd_conf" type="password" name="pwd-c" required onkeyup="confirm_check(this,'pwdc-y','pwdc-n','pwdc-ny')"></td>
                            <td>
                                <img id='pwdc-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='pwdc-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='pwdc-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                        </tr>
                        <tr>
                            <td>Nome:</td>
                            <td><input type="text" name="nom" placeholder="Nome" onkeyup="pattern_validation(this,1,'nom-y','nom-n','nom-ny')"></td>
                            <td>
                                <img id='nom-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='nom-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='nom-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                            <td><input type="checkbox" name="check_list[]" value="nome"></td>
                        </tr>
                        <tr>
                            <td>Cognome:</td><td><input type="text" name="cog" placeholder="Cognome" onkeyup="pattern_validation(this,1,'cog-y','cog-n','cog-ny')"></td>
                            <td>
                                <img id='cog-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='cog-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='cog-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                            <td><input type="checkbox" name="check_list[]" value="cognome"></td>
                        </tr>
                        <tr>
                            <td>Nickname:</td>
                            <td><input type="text" name="nick" placeholder="Nickname"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Data di nascita*:</td>
                            <td><input type="date" name="dataNa" required onchange="check_date(this,'date-y','date-n','date-ny')"></td>
                            <td>
                                <img id='date-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='date-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='date-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                            <td><input type="checkbox" name="check_list[]" value="dataNascita"></td>
                        </tr>
                        <tr>
                            <td>Sesso:</td>
                            <td class="left">
                                <select name="sex">
                                    <option value="Maschio">Male</option>
                                    <option value="Femmina">Female</option>
                                </select>
                            </td>
                            <td></td>
                            <td><input type="checkbox" name="check_list[]" value="sesso"></td>
                        </tr>
                        <tr>
                            <td>Città:</td>
                            <td><input type="text" name="cit" placeholder="Città" onkeyup="pattern_validation(this,1,'cit-y','cit-n','cit-ny')"></td>
                            <td>
                                <img id='cit-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='cit-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='cit-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                            <td><input type="checkbox" name="check_list[]" value="città"></td>
                        </tr>
                        <tr>
                            <td>Luogo di nascita:</td>
                            <td><input type="text" name="citNa" placeholder="Luogo di nascita" onkeyup="pattern_validation(this,1,'citn-y','citn-n','citn-ny')"></td>
                            <td>
                                <img id='citn-y' src="../sources/images/yes.png" alt="yes" width="27px" style='display: none'>
                                <img id='citn-n' src="../sources/images/no.png" alt="no" width="25px" style='display: none'>
                                <img id='citn-ny' src="../sources/images/blank.png" alt="no" width="25px" style='display: block'>
                            </td>
                            <td><input type="checkbox" name="check_list[]" value="città"></td>
                        </tr>
                        <tr>
                            <td colspan="4"><input name="btnsubmit" type="submit"></td>
                        </tr>
                    </table>
                </form>
            </div>

        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/user_info_validation.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>

</body>

</html>