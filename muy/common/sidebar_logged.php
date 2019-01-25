<?php
    include_once "modal_channel.php"
?>

<label for="nav-toggle" class="burger" onclick="hide(getElementById('nav-toggle'))">
    <!-- logo hamburger -->
    <div class="ham"></div>
    <div class="ham"></div>
    <div class="ham"></div>
</label>

<nav style="display: none">
    <input type="checkbox" id="nav-toggle" hidden>
    <!--<label for="nav-toggle" class="burger">-->
    <span id="nasc">
    <!--<span id="nasc" style="left: -300px">-->
    <ul>
        <li><a href="../frontend/home.php">Home</a></li>
        <li class="no_link">Display<hr></li>
        <li><a href="../frontend/home.php">Top visited</a></li>
        <li><a href="../frontend/top-users.php">Top users</a></li>
        <li><a href="../frontend/top-categories.php">Top categories</a></li>
        <li><a href="../frontend/etichette.php">Etichette</a></li>
        <li><a href="../frontend/ultima_giornata.php">Ultima giornata</a></li>
        <li class="no_link">Account<hr></li>
        <?php
            echo "<li><a href='../frontend/user.php?user=".htmlentities(urlencode($_SESSION["email"]))."'>".$_SESSION["nome"]."</a></li>";
        ?>
        <li><a href="../frontend/friends_list.php">Amici</a></li>
        <li><a href="../frontend/coetanei_vicini.php">Coetanei vicini</a></li>
        <li><a href="../frontend/user_impostazioni.php">Impostazioni</a></li>
    </ul>
    </span>
</nav>

<script>
    function hide(elem) {
        if(elem.parentElement.style.display == 'none')
            elem.parentElement.style.display = 'block'
        else
            elem.parentElement.style.display = 'none'
    }
</script>