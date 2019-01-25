function request_fr(button,user){
    par='receiver='+user
    xhr=open_xml_post(muy+"/backend/fr_request.php")
    button.style.display='none'
    button.disabled=true
    xhr.onreadystatechange=function(){
        if(xhr.readyState==4 && xhr.status==200){
            
            console.log(xhr.responseXML)
            var error=xhr.responseXML.getElementsByTagName('error')[0]
            
            if(error.getAttribute('triggered')=='true')
                append_error_atop(error.childNodes[0].childNodes[0].nodeValue)
            else{
                button.innerHTML='In attesa di conferma'
                button.id='disabled'
                button.disabled=true
                button.style.display='inline-block'
            }
        }
    }
    xhr.send(par)
}

function refresh_friendslist(action,button){
    var table={'pending':'pending_view','friends':'friends_view','suggest':'suggestions_view'}
    xhr=ajaxRequest()
    xhr.open("GET",muy+"/common/refresh_fr_list.php?action="+action+"&next="+button.value)
    xhr.responseType='text'
    button.remove()
    xhr.onreadystatechange=function(){
        if(xhr.readyState==4 && xhr.status==200){
            //cercare l'ultimo elemento di blocco con classe four_more per inserire i nuovi risultati
            //search_the_last(document.getElementsByClassName(table[action])[0].lastChild,'four_more').innerHTML=xhr.responseText
            var w_b=document.createElement('div')
            w_b.innerHTML=xhr.responseText
            document.getElementsByClassName(table[action])[0].appendChild(w_b)
        }
    }
    xhr.send()
    
}

function up_status(action,object,button,name){
    var cons={'accept':'Amici','deny':'Bloccato','erase':'Bloccato'}
    var par='action='+action+"&object="+object
    xhr=open_xml_post(muy+"/backend/up_status.php")
    if((action=='deny'||action=='erase')&&!confirm("Una volta effettuata la cancellazione o il rifiuto, tu e "+name+" non potrete più essere amici"))
        return
    
    if(action=='accept')
        button.nextSibling.remove()
    if(action=='deny')
        button.previousSibling.remove();
    button.style.display='none'
    button.id='disabled'
    button.disabled=true
    xhr.onreadystatechange=function(){
        if(xhr.readyState==4 && xhr.status==200){
            
            console.log(xhr.responseXML)
            var error=xhr.responseXML.getElementsByTagName('error')[0]
            
            if(error.getAttribute('triggered')=='true')
                append_error_atop(error.childNodes[0].childNodes[0].nodeValue)
            else{
                button.innerHTML=cons[action]
                button.style.display='inline-block'
            }
        }
    }
    xhr.send(par)
}

function del_pending(button){
    if(confirm("Una volta effettuata l'operazione tutte le richieste da te inviate saranno ritirate")){
        xhr=ajaxRequest()
        console.log('ya')
        xhr.open("GET",muy+"/backend/delete_pending.php")
        button.disabled=true
        button.style.display='none'
        xhr.onreadystatechange=function(){
            if(xhr.readyState==4 && xhr.status==200){
            
                console.log(xhr.responseText)
                var error=JSON.parse(xhr.responseText)
            
                if(error.error)
                    append_error_atop(error.childNodes[0].childNodes[0].nodeValue)
                else
                    button.innerHTML="Eliminate con successo"
                    button.style.display='inline-block'
            }
        }
        xhr.send()
    }
}
