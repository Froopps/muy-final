<?php
    if(isset($_GET["linklogin"])){
        echo "<style>
            .modal_bg{
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                z-index: 2;
                top: 0px;
                left: 0px;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4)
            }
            </style>";
    }
?>

<div id="modal_bg_1" class="modal_bg">
    <div class="modal_wrapper">
        <div class="closure_cross_container">
            <span class="closure_cross" onclick="document.getElementById('modal_bg_1').style.display='none'">&times</span>
        </div>
        <form class="modal_login" method="post" action="../backend/login-check.php">
            <div class="modal_group">
                <label for="login">Username:</label>
                <input type="text" class="modal_text" name="login" required>
            </div>
            <div class="modal_group">
                <label for="pwd">Password:</label>
                <input type="password" class="modal_text" name="pwd" required>
            </div>
            <div class="modal_group">
                <input class="modal_button" type="submit" value="Sign in">
                <a class="modal_button" href="../frontend/signup.php">Registrati</a>
            </div>
        </form>
        <div class="closure_cross_container"></div>
    </div>
</div>
<script>
    var el=document.getElementById("modal_bg_1")
    window.onclick=function(event){
        if(event.target==el)
            el.style.display='none'
    }
</script>