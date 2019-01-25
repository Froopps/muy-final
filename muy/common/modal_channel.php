<div id="modal_bg_2" class="modal_bg">
    <div class="modal_wrapper">
        <div class="closure_cross_container">
            <span class="closure_cross" onclick="document.getElementById('modal_bg_2').style.display='none'">&times</span>
        </div>
        <div class="modal_login">
            <div class="modal_group">
                <label for="channel_name">Nome Canale:</label>
                <input type="text" class="modal_text" name="channel_name" required>
            </div>
            <div class="modal_group">
                <label for="label">Etichetta:</label>
                <input type="text" class="modal_text" name="label" placeholder="inserisci etichette separate da virgole">
            </div>
            <select class="src_type sel_channel_vis" name="channel_type">
                <option value="public">Pubblico</option>
                <option value="social">Social</option>
                <option value="private">Privato</option>
            </select>
            <div class="modal_group"><input class="modal_button"  type="submit" value="Crea" onclick="new_channel('channel_name','label','channel_type')"></div>
        </div>
        <div class="closure_cross_container"></div>
    </div>
</div>
<script>
    var el=document.getElementById("modal_bg_2")
    window.onclick=function(event){
        if(event.target==el)
            el.style.display='none'
    }
</script>