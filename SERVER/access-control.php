<?php

    $method = $_SERVER["REQUEST_METHOD"];

    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Credentials: true");
    
    // Den sk. preflight förfrågan ("får jag anropa dig")
    if ($method === "OPTIONS") {
        // Tillåt alla (origins) och alla headers
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: content-type ");
        exit();
    }

?>