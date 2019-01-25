//crop_image(container(could be an img,div or something),input type file to retrieve the file selected,button for submission)
function crop_image(in_file,image,button){
    if(in_file.files && in_file.files[0]){
        var reader=new FileReader()
        reader.onload=function(event){
            var box=new Croppie(image,{
                viewport:{width:100, height:100, type: 'circle'},
                boundary: {width:200, height:200}
            })
            var url_read=event.target.result
            box.bind({
                url: url_read
            })
            if(button!='no'){
                button.onclick=submit_with_crop
                function submit_with_crop(){
                    button.disable=true
                    box.result({
                        type: 'blob',
                        size: 'original'
                    }).then(function(blob){
                        sub(blob)
                    })
                }
                function sub(img){
                    var newF=new FormData()
                    newF.append('cropped_pro_pic',img,'pro_pic')
                    xhr=ajaxRequest()
                    button.style.display='none'
                    button.disabled=true
                    //altough the name may let think differently, the function uploads content preview image too 
                    xhr.open("POST",muy+"/backend/pro_pic_update.php",true)
                    xhr.onreadystatechange=function(){
                        if(xhr.readyState==4 && xhr.status==200){
                            var error=xhr.responseXML.getElementsByTagName('error')[0]
                                if(error.getAttribute('triggered')=='true')
                                append_error_atop(error.childNodes[0].childNodes[0].nodeValue)
                            else{
                                button.innerHTML='ok'
                                button.style.display='inline-block'
                            }
                        }
                    }
                    xhr.send(newF)
                }
            }
        }
        reader.readAsDataURL(in_file.files[0])
    }
}

function croppieAnteprima(in_file,image){
    var reader=new FileReader()
    //reader.onload=function(event){
        var box=new Croppie(image,{
            viewport:{width:100, height:100, type: 'circle'},
            boundary: {width:200, height:200}
        })
        //console.log(in_file.toDataURL())
        //var url_read=event.target.result
        box.bind({
            url: in_file
        })
    //}
    //reader.readAsArrayBuffer(in_file)
}

//set the default foto
function set_def_foto(button){

    var par="default=1"
    xhr=ajaxRequest()
        xhr.open("POST",muy+"/backend/pro_pic_update.php",true)
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded')
        xhr.onreadystatechange=function(){
            if(xhr.readyState==4 && xhr.status==200)
                button.innerHTML="ok"
        }
        xhr.send(par)
}

function update_user_info(attribute,button){

    var par="attribute="+attribute.name+"&value="+attribute.value
     console.log(attribute.name,' ',attribute.value)
    xhr=ajaxRequest()
    xhr=open_xml_post(muy+"/backend/validate_new_info.php")
    button.style.display='none'
    button.disabled=true
    xhr.onreadystatechange=function(){
        if(xhr.readyState==4 && xhr.status==200){

            console.log(xhr.response)
            var error=xhr.responseXML.getElementsByTagName('error')[0]

            if(error.getAttribute('triggered')=='true')
                append_error_atop(error.childNodes[0].childNodes[0].nodeValue)
            else{
                button.innerHTML="ok"
                button.style.display='inline-block'
            }
        }
    }
    xhr.send(par)

}

function delete_content(element,content){
    
    var par = "id="+content

    if(confirm("Conferma eliminazione")){
        xhr = ajaxRequest()
        xhr.onreadystatechange = function(){
            if(xhr.readyState==4 && xhr.status==200){
                var response = xhr.responseText
                console.log(response)
                element.parentElement.parentElement.style.display = "none"
            }
        }
        xhr.open("POST",muy+"/backend/delete_content.php",true)
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded')
        xhr.send(par)
    }
    
}

function comment(comment,content,list,author,pic,email){

    xhr = ajaxRequest()
    xhr.onreadystatechange = function(){
        if(xhr.readyState==4 && xhr.status==200){
            var response = xhr.responseText
            if(response=="sign in")
                alert("Accedi per commentare")
            else if(response=="no comment")
                alert("Scrivi un commento prima")
            else if(response=="too many")
                alert("Puoi commentare al massimo 3 volte!")
            else{
                addComment(list,author,comment.value,pic,email,response)
                comment.value = ""
                list.scrollTop = list.scrollHeight
                //list.animate({
                //   list.scrollTop = list.scrollHeight
                //}, 300)
                if(document.getElementById("no-comment")!=undefined)
                    document.getElementById("no-comment").style.display = "none"
            }
        }
    }
    xhr.open("POST",muy+"/backend/comment_script.php",true)
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded')
    xhr.send("commento="+comment.value+"&id="+content)
    
}

function addComment(lista,autore,commento,pic,email,id){

    var com = document.createElement("div")
    com.classList.add("commento")
    lista.appendChild(com)
    
    var testa = document.createElement("div")
    testa.classList.add("comm-head")
    com.appendChild(testa)
    
    var tesx = document.createElement("div")
    tesx.classList.add("flex-center")
    testa.appendChild(tesx)
    
    var node1 = document.createElement("a")
    node1.href = "user.php?user="+email
    tesx.appendChild(node1)
    
    var node2 = document.createElement("img")
    node2.classList.add("comm-img")
    node2.src = pic
    node1.appendChild(node2)
    
    node2 = document.createElement("a")
    node2.classList.add("comm-aut")
    node2.href = "user.php?user="+email
    tesx.appendChild(node2)
    
    node1 = node2
    node2 = document.createElement("b")
    var textnode = document.createTextNode(autore)
    node2.appendChild(textnode)
    node1.appendChild(node2)
    
    
    node2 = document.createElement("div")
    testa.appendChild(node2)
    
    node1 = node2
    node2 = document.createElement("button")
    node2.classList.add("delete-cross")
    node2.type = "button"
    node2.addEventListener("click",function(){delete_comment(id,email,this)})
    textnode = document.createTextNode("x")
    node2.appendChild(textnode)
    node1.appendChild(node2)
    
    node2 = document.createElement("div")
    node2.classList.add("comm-text")
    textnode = document.createTextNode(commento)
    node2.appendChild(textnode)
    com.appendChild(node2)

}

function delete_comment(id,email,commento){
        
    xhr = ajaxRequest()
    xhr.onreadystatechange = function(){
        if(xhr.readyState==4 && xhr.status==200){
            var response = xhr.responseText
            if(response=="denied")
                alert("Accesso negato")
            if(response=="ok"){
                commento = commento.parentElement.parentElement.parentElement
                commento.style.display = "none"
                if(commento.previousElementSibling==null){
                    var div = document.createElement("div")
                    div.id = "no-comment"
                    var textnode = document.createTextNode("Nessun commento")
                    div.appendChild(textnode)
                    commento.parentElement.appendChild(div)
                }
                else if(commento.previousSibling.previousSibling==document.getElementById("no-comment"))
                    document.getElementById("no-comment").style.display = "block"
            }
        }
    }
    xhr.open("POST",muy+"/backend/comment_delete.php",true)
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded')
    xhr.send("id="+id+"&email="+email)

}

function visual(id){
//leva se togli visual ajax
    xhr = ajaxRequest()
    xhr.onreadystatechange = function(){
        if(xhr.readyState==4 && xhr.status==200){
                var response = xhr.responseText
                document.getElementById("visual").innerHTML=response
        }
    }
    xhr.open("GET",muy+"/backend/visual.php?id="+id,true)
    xhr.send()
}

function add_eti(id,button){
    
    var in_text = document.getElementsByName("newtag")[0]
    if(in_text.type == "hidden"){
        in_text.type = "text"
        document.getElementsByClassName("cross_button")[0].style.display = "block"
    }else if(in_text.type == "text"){
        xhr = ajaxRequest()
        xhr.onreadystatechange = function(){
            if(xhr.readyState==4 && xhr.status==200){
                var response = xhr.responseText
                if(response=="denied"){
                    alert("Accesso negato")
                    in_text.value = ""
                }else if(response=="no_tag")
                    alert("Inserisci almeno un'etichetta")
                else if(response=="err_tag"){
                    alert("Uno o più tag non accettabili")
                    in_text.value = ""
                }
                else if(response=="err_db")
                    alert("Errore nella connessione con il database")
                else if(response=="tag_dup"){
                    alert("Etichetta già inserita")
                    in_text.value = ""
                }else{
                    document.getElementsByClassName("cross_button")[0].style.display = "none"
                    in_text.value = ""
                    in_text.type = "hidden"
                    
                    var span = document.createElement("span")
                    span.classList.add("eti-span")
                    if(button.previousSibling.previousSibling==null)
                        button.parentElement.insertBefore(span,button.parentElement.firstChild)
                    else
                        button.parentElement.insertBefore(span,in_text)
                    
                    var node = document.createElement("a")
                    node.classList.add("etichetta")
                    node.href = "categoria.php?tag=%23"+response
                    var textnode = document.createTextNode("#"+response)
                    node.appendChild(textnode)
                    span.appendChild(node)
                    //cross
                    node = document.createElement("button")
                    node.classList.add("cross-but")
                    node.type = "button"
                    node.addEventListener("click",function(){del_eti("#"+response,id,this)})
                    node.appendChild(document.createTextNode("x"))
                    span.appendChild(node)
                }
            }
        }
        xhr.open("POST",muy+"/backend/add_tag.php",true)
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded')
        xhr.send("id="+id+"&tag="+in_text.value)
    }
    
}

function del_eti(eti,id,elem){
    
    xhr = ajaxRequest()
    xhr.onreadystatechange = function(){
        if(xhr.readyState==4 && xhr.status==200){
            var response = xhr.responseText
            if(response=="denied")
                alert("Accesso negato")
            else{
                elem.previousSibling.style.display = "none"
                elem.style.display = "none"
            }
        }
    }
    xhr.open("POST",muy+"/backend/delete_tag.php",true)
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
    xhr.send("id="+id+"&tag="+eti)
    
}


function ground_like_stars(){
    var starz=document.getElementsByClassName('ili')
    for(i=0;i<5;i++){
        console.log(i)
        starz[i].style.backgroundImage="url(../sources/images/star-e.png)"
    }
}

function up_like_stars(value){
    var starz=document.getElementsByClassName('ili')
    for(i=0;i<value;i++)
        starz[i].style.backgroundImage="url(../sources/images/star-f.png)"
    for(i=value;i<starz.length-1;i++)
        starz[i].style.backgroundImage="url(../sources/images/star-e.png)"
}

function like_it(percorso,voto){

    console.log(percorso,voto)
    var par="relativoA="+percorso+"&voto="+voto
    xhr=ajaxRequest();
    xhr.open("POST",muy+"/backend/like_it.php")
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
    xhr.onreadystatechange = function(){
        if(xhr.readyState==4 && xhr.status==200){
            var res=JSON.parse(xhr.responseText)
            if(res.error)
                alert('Errore nella connessione')
            else if(this.value=='0')
                ground_like_stars()
            else
                up_like_stars(voto)
        }
    }
    xhr.send(par)
}

function new_channel(name,label,type){

    name = document.getElementsByName(name)[0]
    nome = name.value
    
    label = document.getElementsByName(label)[0]
    type = document.getElementsByName(type)[0]
    var par = "channel_name="+name.value+"&label="+label.value+"&channel_type="+type.value
    
    xhr = ajaxRequest()
    xhr.onreadystatechange = function(){
        if(xhr.readyState==4 && xhr.status==200){
            var response = xhr.responseText
            if(response=="db_err")
                alert("Errore nella connessione con il database")
            else if(response=="data_miss")
                alert("Inserire tutti i dati necessari")
            else if(response=="duplicate")
                alert("Hai già un canale con questo nome")
            else if(response=="long")
                alert("Il nome del canale è troppo lungo")
            else if(response=="type_err")
                alert("Esprimere un valore di visibilità sensato")
            else{
                if(document.getElementById("no-ch")!=null)
                    document.getElementById("no-ch").style.display = 'none'
                
                document.getElementById("modal_bg_2").style.display = 'none'

                var canale = document.createElement("div")
                canale.classList.add("categoria")
                document.getElementsByClassName("content")[0].appendChild(canale)

                var head = document.createElement("div")
                head.classList.add("categoria_user_nome")
                canale.appendChild(head)

                var node1 = document.createElement("a")
                node1.classList.add("categoria_titolo")
                node1.href = "canale.php?nome="+name.value+"&proprietario="+response
                var textnode = document.createTextNode(name.value)
                node1.appendChild(textnode)
                head.appendChild(node1)

                node1 = document.createElement("div")
                node1.classList.add("flex-center")
                head.appendChild(node1)

                var node2 = document.createElement("button")
                node2.classList.add("delete_button")
                node2.addEventListener("click",function(){delete_channel(this,nome,response)})
                node1.appendChild(node2)

                node2 = document.createElement("a")
                node2.classList.add("glyph-button")
                node2.href = "upload.php?canale="+name.value
                node1.appendChild(node2)

                node1 = node2
                node2 = document.createElement("img")
                node2.src = "../sources/images/plus.png"
                node2.alt = "Aggiungi"
                node2.width = "30"
                node1.appendChild(node2)

                node1 = document.createElement("hr")
                node1.align = "left"
                canale.appendChild(node1)

                node1 = document.createElement("div")
                node1.classList.add("eticanale")
                canale.appendChild(node1)

                node2 = document.createElement("div")
                node2.classList.add("can-vis")
                textnode = document.createTextNode("#"+type.value)
                node2.appendChild(textnode)
                node1.appendChild(node2)
                
                var eti = getEtichetteCanale(label.value)
                for(i=0;i<eti.length;++i){
                    node2 = document.createElement("a")
                    node2.classList.add("etichetta")
                    node2.id = "no_link"
                    textnode = document.createTextNode("#"+eti[i])
                    node2.appendChild(textnode)
                    node1.appendChild(node2)
                }

                node1 = document.createElement("div")
                node1.classList.add("scrollbar")
                canale.appendChild(node1)

                node2 = document.createElement("span")
                node2.classList.add("message_span")
                textnode = document.createTextNode("Non c'è nessun elemento da mostrare")
                node2.appendChild(textnode)
                node1.appendChild(node2)

                name.value = ""
                label.value = ""
                type.value = "public"
            }
        }
    }
    xhr.open("POST",muy+"/backend/new_channel.php",true)
    xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded')
    xhr.send(par)

}

function getEtichetteCanale(str){
    
    var res = []
    var tags = str.split(",")
    for(i=0;i<tags.length;++i){
        if(tags[i]!=""){
            while(tags[i][0]==" "&&tags[i].length>1)
                tags[i] = tags[i].substring(1)
            //doppi spazi interni
            tags[i].replace(/\s{2,}/g,' ');
            if(tags[i]!=""&&tags[i]!=" ")
                res.push(tags[i])
        }
    }
    return res
}

function delete_channel(button,channel,user){

    if(confirm("Conferma eliminazione")){
        xhr = ajaxRequest()
        xhr.onreadystatechange = function(){
            if(xhr.readyState==4 && xhr.status==200){
                var response = xhr.responseText
                if(response=="denied")
                    alert("Accesso negato")
                else
                    button.parentElement.parentElement.parentElement.style.display = "none"
            }
        }
        xhr.open("POST",muy+"/backend/delete_channel.php",true)
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded')
        xhr.send("nome="+channel+"&proprietario="+user)
    }
}