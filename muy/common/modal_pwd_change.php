<div id="modal_bg_3" class="modal_bg">
    <div class="modal_wrapper">
        <div class="closure_cross_container">
            <span class="closure_cross" onclick="document.getElementById('modal_bg_3').style.display='none'">&times</span>
        </div>
        <div class="modal_login">
            <div class="modal_group"><label for="pwd">Password attuale:</label></div>
            <div class="modal_group"><input type="password" class="modal_text old_pwd" required></div>
            <div class="modal_group"><button class="modal_button" type="button" onclick="old_pwd_check(document.getElementsByClassName('old_pwd')[0].value)">Valida</button></div>
        </div>
        <div class="closure_cross_container"></div>
    </div>
</div>
<script>
    var el=document.getElementById("modal_bg_3")
    window.onclick=function(event){
        if(event.target==el)
            el.style.display='none'
    }

</script>

<div id="modal_bg_4" class="modal_bg">
    <div class="modal_wrapper">
        <div class="closure_cross_container">
            <span class="closure_cross" onclick="document.getElementById('modal_bg_4').style.display='none'">&times</span>
        </div>
        <div class="modal_login">
            <div class="modal_group"><label for="pwd">Conferma password:</label></div>
            <div class="modal_group"><input type="password" class="modal_text new_conf_pwd" required></div>
            <div class="modal_group"><button class="modal_button" type="button" onclick="conf_pass_def(document.getElementsByClassName('new_conf_pwd')[0].value,document.getElementsByClassName('in_pass_up')[0],document.getElementById('pass_butt'))">Valida</button></div>
        </div>
        <div class="closure_cross_container"></div>
    </div>
</div>
<script>
    var el=document.getElementById("modal_bg_4")
    window.onclick=function(event){
        if(event.target==el)
            el.style.display='none'
    }

</script>