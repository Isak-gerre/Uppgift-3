<?php
    // Ändra caption
    // Ändra totala_likes
    // Ändra likes



    error_reporting(-1);
    require_once "access-control.php";
    require_once "functions.php";

    // Ladda in vår JSON data från vår fil
    $users = loadJSON("DATABAS/users.json");
    $posts = loadJSON("DATABAS/posts.json");

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
    $userID = $requestData["userID"];
    $caption = $requestData["caption"];
    $found = false;
    $foundUser = null;

    // Ändrar caption
    $posts["posts"][$postID]["caption"] = $caption;

    foreach ($posts["posts"] as $index => $post) {
        if ($post["id"] == $postID) {
            $found = true;
    
            if (isset($requestData["caption"])) {
                $user["caption"] = $caption;
            }
    
            // Uppdatera vår array
            $posts[$index] = $user;
            $foundUser = $user;
            
            break;
        }
    }

    if ($found === false) {
        send(
            [
                "code" => 5,
                "message" => "The users by `id` does not exist"
            ],
            404
        );
    }
    
    saveJson("users.json", $users);
    send($foundUser);
// =============================================================
