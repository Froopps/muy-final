function suggestions_search(){
    var bar=document.getElementById('src')
    var type=document.getElementById('src_block').elements['src_type'].value
    var list=document.getElementsByClassName('sug_list')[0]
    var block=document.getElementsByClassName('sug_block')[0]
    var value=escape_sharp(bar.value)
    if(value.length<=1){
        block.style.display='none'
        list.style.display='none'
    }
    else{
        var xhr=ajaxRequest()
        xhr.open("GET",muy+"/backend/get_suggestions.php?table="+type+"&pattern="+value)
        xhr.responseType='text'
        block.style.display='flex'
        list.style.display='block'
        xhr.onreadystatechange=function(){
            if(xhr.readyState==4 && xhr.status==200){
                console.log(xhr.responseText)
                document.getElementsByClassName('sug_list')[0].innerHTML=xhr.responseText
            }
        }
        xhr.send()
    }
}

function autocomp(value){
    var list=document.getElementsByClassName('sug_list')[0]
    var block=document.getElementsByClassName('sug_block')[0]
    document.getElementById('src').value=value
    block.style.display='none'
    list.style.display='none'

}

//molto simile alla funzione per fare il refresh della lista degli amici riscritta per evitare di aggiungere
//per mantenere concettualmente separate
function refresh_search_res(action,button,pattern){
    xhr=ajaxRequest()
    xhr.open("GET",muy+"/common/refresh_search_list.php?action="+action+"&next="+button.value+"&pattern="+pattern)
    xhr.responseType='text'
    button.remove()
    xhr.onreadystatechange=function(){
        if(xhr.readyState==4 && xhr.status==200){
            //cercare l'ultimo elemento di blocco con classe four_more per inserire i nuovi risultati
            var w_b=document.createElement('div')
            w_b.innerHTML=xhr.responseText
            document.getElementsByClassName('search_results')[0].appendChild(w_b)
        }
    }
    xhr.send()
}
