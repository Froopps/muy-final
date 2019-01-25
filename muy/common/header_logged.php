<header>
    <span>
        <a id="logo" href="../frontend/home.php"></a>
        <a id="primo" href="../frontend/upload.php">Upload</a>
    </span>
    <form autocomplete='off' id="src_block" action="search_results.php" method="get" onsubmit='escape_on_submit(this)'>
        <select name="src_type">
            <option value="oggettoMultimediale">Contenuto</option>
            <option value="utente">Utente</option>
            <option value="canale">Canale</option>
            <option value="categoria">Tag</option>
        </select>
        <input id="srcbtn" type="submit">
        <input id="src" type="text" name="src_txt" placeholder="Cerca..." onkeyup="suggestions_search()">
    </form>
    <span>
        <?php
            echo "<a id='head_usr' href='../frontend/user.php?user=".htmlentities(urlencode($_SESSION["email"]))."'>".$_SESSION["nome"]."</a>";
        ?>
        <a href="../backend/esci.php">Esci</a>
    </span>
</header>
<div class='sug_block'>
    <ul class='sug_list'>
    </ul>
</div>