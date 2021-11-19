<?php
    // Ändra caption
    // Ändra totala_likes
    // Ändra likes

    error_reporting(-1);
    require_once "access-control.php";
    require_once "functions.php";

    // Ladda in vår JSON data från vår fil
    $usersDB = loadJSON("DATABAS/users.json");
    $postsDB = loadJSON("DATABAS/posts.json");
    $users = $usersDB["users"];
    $posts = $postsDB["posts"];


    // HTTP-metod
    // Content-Type
    $method = $_SERVER["REQUEST_METHOD"];
    $contentType = $_SERVER["CONTENT_TYPE"];

    // Hämta ut det som skickades till vår server
    // Måste användas vid alla metoder förutom GET
    $data = file_get_contents("php://input");
    $requestData = json_decode($data, true);
    

    // 1. Kollar om det är rätt metod
    if ($method !== "PATCH") {
        $message = [
            "code" => 1,
            "message" => "Method Not Allowed"
        ];    
        send($message, 405);
    }
   
    // 2. Kollar om Content-TYPE = JSON
    if ($contentType !== "application/json"){
        $message = [
            "code" => 2,
            "message" => "The API only accepts JSON"
        ];
        send($message, 404);
    }

    // 3. Kollar om båda värdena finns som nycklar
    if (!isset($requestData["caption"]) || empty($requestData["caption"])) {
        $message = [
            "code" => 3, 
            "message" => "No caption added"
        ];
        send($message, 400);
    }

    
    $postID = $requestData["postID"];
    $caption = $requestData["caption"];

    inspect($posts[$postID]["caption"]);
    inspect($caption);
    // Ändrar caption
    $posts[$postID]["caption"] = $caption;
    inspect($posts[$postID]["caption"]);
    
    // Sparar i Databasen
    $postsDB["posts"] = $posts;
    inspect($postsDB);
    saveJSON("DATABAS/posts.json", $postsDB);
    $message = [
        "code" => 4,
        "message" => "SUCCESS"
    ];
    send($message);
    

