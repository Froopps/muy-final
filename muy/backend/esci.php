<?php
    include_once realpath($_SERVER["DOCUMENT_ROOT"]."/muy/common/setup.php");
    session_start();
    session_destroy();
    header("Location:$location/frontend/home.php?msg=Arrivederci");
?>