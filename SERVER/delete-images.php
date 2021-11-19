<?php
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
    $postID = $requestData["postID"];
    $userID = $requestData["userID"];

    
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
    // 3. Kollar om båda värdena finns som nycklar
    if (!isset($requestData["postID"], $requestData["userID"]) || empty($postID) || empty($userID)) {
        $message = [
            "code" => 3, 
            "message" => "There went something wrong with the keys, check that again"
        ];
        send($message, 400);
    }
    // 4. Kollar om bilden finns i databasen 
    if (!array_key_exists($postID, $posts)){
        $message = [
            "code" => 4,
            "message" => "Picture dosn't exist at all"
        ];
        send($message, 404);
    }
    
    // 5. Kollar om posten finns i den inloggades array av posts
    if (array_search($postID, getUserPosts($userID)) === false){
        $message = [
            "code" => 5, 
            "message" => "You can only delete pictures of your own"
        ];
        send($message, 400);
    }

    // Om allt stämmer raderas:
    // 1. Bild-filen från bild mappen
    // 2. ID från användarens array av bilder
    // 3. Bilden från POST databasen
     
    // Radera en bild från image-mappen
    $image_url = $posts[$postID]["image_url"];
    $http_host = $_SERVER["HTTP_HOST"];
    $directory = str_replace("http://$http_host/", "",  $image_url);
    unlink($directory);

    // Raderar postens ID från avändarens array av posts
    $arrayPosts = $users[$userID]["posts"];
    foreach($arrayPosts as $index => $post){
        if($post == $postID){
            array_splice($users[$userID]["posts"], $index, 1);
            break;
        }
    }
    
    // Raderar en post från databasen
    unset($posts[$postID]);
    
    // Uppdaterar filen
    $usersDB["users"] = $users;
    $postsDB["posts"] = $posts;
    saveJSON("DATABAS/users.json", $users);
    saveJSON("DATABAS/posts.json", $posts);

    // Skickar tillbaka meddelande om att allt gick fint
    $message = [
        "message" => "The picture is deleted"
    ];
    send($message);
    
?>