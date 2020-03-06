<?php

function IsPostBack () {
    $isPostBack = false;
    $thisPage="";
    $referer = "";

    if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['PHP_SELF']) && isset($_SERVER['HTTP_REFERER']) ){

        $thisPage =(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $referer = $_SERVER['HTTP_REFERER'];

        if ($referer == $thisPage){
            $isPostBack = true;
        } 

    }
   
    return $isPostBack;

}

function GetCurrentFullURL(){
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    return $actual_link;
}

?>