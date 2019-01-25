var muy='http://localhost/muy'

function ajaxRequest(){
    var request=false;
    try{request=new XMLHttpRequest()}
    catch(e1){
        try{request=new ActiveXObject("Msxml2.XMLHTTP")}
        catch(e2){
            try{request=new ActiveXObject("Microsoft.XMLHTTP")}
            catch(e3){request=false}
        }
    }
    return request
}

function open_xml_post(script){
    xhr=ajaxRequest()
    xhr.open("POST",script)
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
    xhr.responseType='document'
    xhr.overrideMimeType('application/xml')
    return xhr
}

function open_xml_get(script){
    xhr=ajaxRequest()
    xhr.open("GET",script)
    xhr.responseType='document'
    xhr.overrideMimeType('application/xml')
    return xhr
}

function append_error_atop(value){
    var newErSpan=document.createElement('span')
    var container=document.getElementsByClassName('content')[0]
    if(container===undefined)
        container=document.getElementsByClassName('content-centre')[0]
    if(container.firstChild.className=='error_span')
        document.getElementsByClassName('error_span')[0].remove()
    newErSpan.setAttribute('class','error_span')
    newErSpan.appendChild(document.createTextNode(value))
    container.insertBefore(newErSpan,container.firstChild)
}

function search_the_last(el,cl){
    if(el.className==cl)
        return el
    else
        return search_the_last(el.previousSibling,cl)
}

function escape_sharp(s){
    t=''
    for(i=0;i<s.length;i++){
        if(s[i]!="#")t+=s[i]
    }
    return t
}

var escape_on_submit=function escape_on_submit(el){
    el.elements['src_txt'].value=escape_sharp(el.elements['src_txt'].value)
}

