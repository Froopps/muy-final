<header>
    <a id="logo" href="../frontend/home.php"></a>
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
        <!---a href="../frontend/signup.php">Registrati</a--->
        <button class="button_text" onclick="document.getElementById('modal_bg_1').style.display='flex'">Login</button>
    </span>
</header>
<div class='sug_block'>
    <ul class='sug_list'>
    </ul>
</div>
<?php
    include_once "modal_login.php"
?>