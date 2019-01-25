<?php
    # computing a hash value using blowfish; 22 characters salt and 7 iterations by default
    function blowhash($input,$round=7){
        $salt="";
        $salt_values=array_merge(range('A','Z'),range(0,9),range('a','z'));
        for($i=0;$i<22;$i++){
            #takes a random value from the array of salt characters
            $salt.=$salt_values[array_rand($salt_values)];
        }
        return crypt($input, sprintf('$2a$%02d$',$round). $salt);
    }
    #hash matching test
    function hash_match($input,$hashed){
        log_into($input,$hashed,crypt($input,$hashed));
        return crypt($input,$hashed)==$hashed;
    }
    #log into a file
    function log_into($msg){
        $logfile=realpath($_SERVER["DOCUMENT_ROOT"]."/../muy_res/log_files")."/db_error.txt";
        $timestamp=date('Y-m-d H:i:s',time());
        $fp=fopen($logfile,"a");
        fwrite($fp,$msg.$timestamp."\n");
        fclose($fp);
    }
    #set the value for visibility of a user
    function set_visibility($arr){
        $info=array("nome"=>0,"cognome"=>1,"dataNascita"=>2,"sesso"=>3,"città"=>4,"cittàNascita"=>5);
        $visibility_mask=0;
        foreach($arr as $key){
            $visibility_mask+=pow(2,$info[$key]);
        }
        return $visibility_mask;
    }
    #get the list of visible info for a user
    function get_visible_list($vis){
        $info=array("nome","cognome","dataNascita","sesso","citta","cittaNascita");
        $public_list=array();
        foreach($info as $a){
            if($vis%2==0){
                array_push($public_list,$a);
            }
            $vis=(int)$vis/2;
        }
        return $public_list;
    }
    #checking email validity and existence at insertion
    function valid_new_email($value,$connected_db){
        $res=array("error"=>FALSE,"msg"=>"","result"=>"");
        #using a restrictive policy (declare what is valid and not what is not) to increase security and a validation
        #conform to the RFC standards on valid characters in email addresses
        if(!preg_match('/^[A-Za-z0-9\?\.\+\^\[\]\'&~=_-èéàòù ]+@[A-Za-z0-9\?\.\+\^\'&~=_-èéàòù ]+\.{1}[A-Za-z]{2,6}$/',$value)){
            $res["error"]=TRUE;
            $res["msg"]="Mail non valida";
            return $res;
        }
        #verify length
        if(strlen($value)>200){
            $res["error"]=TRUE;
            $res["msg"]="Mail troppo lunga";
            return $res;
        }
        #verify that no accounts are currently associated with this before continuing to process other incoming data
        $email=escape($value,$connected_db);
        $query="SELECT COUNT(*) FROM utente WHERE email='".$email."'";
        $query_res=$connected_db->query($query);
        if(!$query_res){
            $res["error"]=TRUE;
            $res["msg"]="Errore nella connessione con il database";
            log_into("Errore di esecuzione della query ".$query." ".$connected_db->error);
            return $res;
        }
        if($query_res->fetch_row()[0]!=0){
            $res["error"]=TRUE;
            $res["msg"]="Esiste già un account associato alla mail inserita";
            return $res;
        }
        $res["result"]=$email;
        return $res;
    }
    #case sensitiveness(php) and obliviousness(sql) make the world null evil
    function escape($str,$connected_db){
        $str=trim($str);
        if(strtolower($str)=="null"){
            $str="\\".$str;
        }
        return $connected_db->real_escape_string($str);
    }

    function valutazione($content_path,$connected_db){
        $res=array("error"=>FALSE,"msg"=>"","result"=>"");
        $query="SELECT voto,AVG(voto-1) AS media FROM valutazione WHERE relativoA='".escape($content_path,$connected_db)."' AND voto!='0'";
        $query_res=$connected_db->query($query);
        if(!$query_res){
            $res["error"]=TRUE;
            $res["msg"]="db err";
            log_into("Errore di esecuzione della query ".$query." ".$connected_db->error);
            return $res;
        }
        $row=$query_res->fetch_assoc();
        if($query_res->num_rows==0||$row["media"]==null)
            $res["result"]="0.0";
        else{
            $res["result"]=substr($row["media"],0,3);
        }
        log_into($query);
        return $res["result"];
    }

    function trimSpaceBorder($str){
        #spazi inizio
        while($str[0]==" "&&strlen($str)>1)
            $str=substr($str,1);
        #spazi fine
        while(substr($str,-1)==" "&&strlen($str)>1)
            $str=substr($str,0,-1);
        return $str;
    }

    function trimSpace($str){
        trimSpaceBorder($str);
        #multipli spazi interni
        $str=preg_replace('/\s+/', ' ',$str);
        return $str;
    }

    function toUpperFirst($str){
        $str=strtoupper($str[0]).substr($str,1);
        return $str;
    }

    function ritaglia($input,$output){
        $image = imagecreatefromstring(file_get_contents($input));
        $size = min($x=imagesx($image),$y=imagesy($image));
        #riaglia un quadrato al centro dell'immagine
        if($size<164){
            if($x==$y)
                $image2 = imagecrop($image,["x"=>0,"y"=>0,"width"=>$size,"height"=>$size]);
            else if($x>$y)
                $image2 = imagecrop($image,["x"=>($x-$y)/2,"y"=>($y-$size)/2,"width"=>$size,"height"=>$size]);
            else
                $image2 = imagecrop($image,["x"=>0,"y"=>($y-$x)/2,"width"=>$size,"height"=>$size]);
        }else{
            if($x==$y) 
                $image2 = imagecrop($image,["x"=>0,"y"=>0,"width"=>$size,"height"=>$size]);
            else if($x>$y)
                $image2 = imagecrop($image,["x"=>($x-$y)/2,"y"=>0,"width"=>$size,"height"=>$size]);
            else
                $image2 = imagecrop($image,["x"=>0,"y"=>($y-$x)/2,"width"=>$size,"height"=>$size]);
        }
        imagepng($image2,$output);
        imagedestroy($image);
        imagedestroy($image2);
    }

    function getDuration($video,$ffmpeg){
        #get video info
        $cmd=$ffmpeg." -i \"".$video."\" 2>&1";
        exec($cmd,$vidinfo);
        #find duration info
        $cont=0;
        foreach($vidinfo as $riga){
            if(strrpos($riga,"Duration"))
                break;
            else
                $cont++;
        }
        $riga=explode(":",$vidinfo[$cont]);
        $hh=$riga[1];
        $mm=$riga[2];
        $ss=substr($riga[3],0,2);
        $time=$hh*3600+$mm*60+$ss;
        return $time;
    }

    function getYoutubeId($url){
        $edit=explode("v=",$url);
        $id=explode("&",$edit[1]);
        return($id[0]);
    }
?>