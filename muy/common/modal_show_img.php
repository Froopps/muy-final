<div id="modal_bg_img" class="modal_bg">
    <div class="modal_wrapper">
        <div class="closure_cross_container">
            <span class="closure_cross" onclick="document.getElementById('modal_bg_img').style.display='none'">&times</span>
        </div>
        <div class="modal_login">
            
        </div>
        <div class="closure_cross_container"></div>
    </div>
</div>
<script>
    var el=document.getElementById("modal_bg_img")
    window.onclick=function(event){
        if(event.target==el)
            el.style.display='none'
    }

</script>