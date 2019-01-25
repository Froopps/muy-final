<?php
    session_start();
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");

    if($_GET["action"]=='user'){
        $res=get_top_vip($connected_db);
        $s="utenti-top";
        $n="top_users.xml";
		$id="email";
    }
    else{
        $res=get_top_tags($connected_db);
        $s="categorie-top";
        $n="top_categories.xml";
		$id="tag";
    }

    header("Content-type: text/xml; charset=utf-8");
    #per forzare il download lato client
    header("Content-Disposition: attachment; filename='$n'");
    echo "<?xml version='1.0'?>\n";
	echo "<$s>\n";
	$row=$res->fetch_assoc();
    if(!$res||!$row[$id]){
		echo"</$s>";
        exit();
    }
    while($row){
        echo"\t<utente-top>\n";
        foreach($row as $key=>$value)
            echo"\t\t<$key>$value</$key>\n";
        echo "\t</utente-top>\n";
		$row=$res->fetch_assoc();
    }
    echo"</$s>";
    exit();
    
?>