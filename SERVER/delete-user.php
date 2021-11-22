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

// Data som skickas med metoden (DELETE)
$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

// 1. Kollar om det är rätt metod
if ($method !== "DELETE") {
    $message = ["message" => "Method Not Allowed"];    
    send($message, 405);
}

// 2. Kollar om Content-TYPE = JSON
if ($contentType !== "application/json"){
    $message = ["message" => "The API only accepts JSON"];
    send($message, 404);
}

// 3. Kollar om värdet är satt eller tomt
if (!isset($requestData["userID"]) || empty($requestData["userID"])) {
    $message = ["message" => "Something went wrong with the key, check that again"];
    send($message, 400);
}

// 4. Kollar om profilen finns i databasen 
if (!array_key_exists($requestData["userID"], $users)){
    $message = ["message" => "The profile dose not exist"];
    send($message, 404);
}

// Om allt stämmer raderas:
// 1. Alla profilens bild-filer
// 2. Alla bilder från databasen
// 3. Profilen från USER databas
 
// Radera bilder från image-mappen & databasen
$userID = $requestData["userID"];
foreach($users[$userID]["posts"] as $userPost){
    foreach($posts as $index => $post){
        if($userPost == $post["id"]){
            $image_url = $post["image_url"];
            $http_host = $_SERVER["HTTP_HOST"];
            $directory = str_replace("http://$http_host/", "",  $image_url);
            
            // Raderar filenfrån mappen
            if(file_exists($directory)){
                unlink($directory);
            }
            // Raderar bilden från databasen
            unset($posts[$post["id"]]);
        }
    }
}

// Raderar ur Followers
$usersV2 = removeUserFromLists($users, $userID, "following", "followers");
// Raderar ur Following
$usersV3 = removeUserFromLists($usersV2, $userID, "followers", "following");

// Raderar likes som användare har likeat tidigare
$postsV2 = removeLikes($posts, $userID);

// Raderar en user från databasen
unset($usersV3[$userID]);

// Uppdaterar filen
$usersDB["users"] = $usersV3;
$postsDB["posts"] = $postsV2;
saveJSON("DATABAS/users.json", $usersDB);
saveJSON("DATABAS/posts.json", $postsDB);

// Skickar tillbaka meddelande om att allt gick fint
$message = [
    "message" => "The profile is deleted, with all of the pictures"
];
send($message);

?>