<?php
    session_start();
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>MUY | Login</title>
    
    <?php include "../common/head.html"; ?>
</head>

<body>

        <?php
            include "../common/header_unlogged.html";
            include "../common/sidebar_unlogged.html";
        ?>

        <main>

            <div class="content">
                <form action="../backend/login-check.php" method="post">
                    <table  id="signup-table">
                        
                    <?php
                        if(isset($_SESSION["logged"])){
                            if(!($_SESSION["logged"])){
                                echo "<tr><td colspan=\"2\">Errore di login, riprova</td></tr>";
                                $_SESSION["logged"]=NULL;
                            }
                        }
                    ?>
                    
                        <tr><td>Login:</td><td><input type="text" name="login" default="Froops" require></td></tr>
                        <tr><td>Password:</td><td><input type="password" name="pwd" default="abc" require></td></tr>
                        <tr><td colspan="2"><input type="submit"></td></tr>
                    </table>
                </form>
            </div>

        </main>
        <script type="text/javascript" src="../common/script/setup.js"></script>
        <script type="text/javascript" src="../common/script/search.js"></script>
</body>

</html>