<?php
    error_reporting(-1);
    require_once "access-control.php";
    require_once "functions.php";

    // Ladda in vår JSON data från vår fil
    $users = json_decode(file_get_contents("DATABAS/users.json"), true);
    $posts = json_decode(file_get_contents("DATABAS/posts.json"), true);

    // Vilken HTTP metod vi tog emot plus CONTENT TYPE
    $method = $_SERVER["REQUEST_METHOD"];
    $contentType = $_SERVER["CONTENT_TYPE"];
    $server = $_SERVER;

    // Hämta ut det som skickades till vår server
    // Måste användas vid alla metoder förutom GET
    $data = file_get_contents("php://input");
    $requestData = json_decode($data, true);
    // $postID = $requestData["postID"];
    // $userID = $requestData["userID"];

    
    // 1. Kollar om det är rätt metod
    if ($method !== "DELETE") {
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
    // 3. Kollar om båda värdet finns som nycklar
    if (!isset($requestData["userID"]) || empty($requestData["userID"])) {
        $message = [
            "code" => 3, 
            "message" => "There went something wrong with the key, check that again"
        ];
        send($message, 400);
    }
    // 4. Kollar om profilen finns i databasen 
    if (!array_key_exists($requestData["userID"], $users["users"])){
        $message = [
            "code" => 4,
            "message" => "The profile is already deleted"
        ];
        send($message, 404);
    }
    

    // Om allt stämmer raderas:
    // 1. Alla profilens bild-filer
    // 2. Alla bilder från databasen
    // 3. Profilen från USER databas
     
    // Radera bilder från image-mappen & databasen
    $userID = $requestData["userID"];

    foreach($users["users"][$userID]["posts"] as $userPost){
        foreach($posts["posts"] as $index => $post){
            if($userPost === $post["id"]){

                $image_url = $post["image_url"];
                $http_host = $_SERVER["HTTP_HOST"];
                $directory = str_replace("http://$http_host/", "",  $image_url);
                
                // Raderar filenfrån mappen
                unlink($directory);

                // Raderar bilden från databasen
                unset($posts["posts"][$post["id"]]);
            }
        }
    }
    
    // Raderar en user från databasen
    unset($users["users"][$userID]);
    
    // Uppdaterar filen
    saveJSON("users.json", $users);
    saveJSON("posts.json", $posts);

    // Skickar tillbaka meddelande om att allt gick fint
    $message = [
        "message" => "The profile is delted, with all the pictures"
    ];
    send($message);
    
?>