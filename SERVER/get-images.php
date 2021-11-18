<!-- 
    •GET
    •Kunna hämta alla entiteter (inga parametrar)
    •Kunna hämta en entitet med parametern id=x
    •Kunna hämta en eller flera entiteter med parametern ids=x,y,z
    •Kunna hämta entiteter med en parameter som representerar någon nyckel ni själva valt, 
    t.ex. om jag hade entiteten "hyresgäster" kunde jag valt parametern age=n som hämtar alla hyresgäster 
    vars ålder är detsamma som n. Så på denna punkt väljer ni själva parametern.
    •Kunna begränsa antal entiteter vi hämtar med parametern limit=n. 
    Denna parametern ska kunna kombineras med andra parametrar.
    •Kunna inkludera relaterade entiteter med parametern include=1, 
    t.ex. om jag haft en hund i form av { name: "Arya", owner: 1 } (där 1 är ett ID) 
    - med denna parametern skulle vi då inkludera relationen så här { name: "Arya", owner: { name: "Sebbe" }}. Denna  av 24
    Databasbaserad publiceringHT21parameter ska kunna kombineras med andra parametrar. Det är ok om detta bara fungerar för er ena entitet 
-->

<?php

error_reporting(-1);
require_once "access-control.php";
require_once "functions.php";   

if($method === "GET" && empty($_GET)){
    getImages();
}

if($method === "GET" && isset($_GET["id"])){
    $id = $_GET["id"];
    var_dump(is_numeric(1));
    if(!is_numeric(($id))){
        echo "error";
        exit();
    }
    getImages($id);
}

?>
