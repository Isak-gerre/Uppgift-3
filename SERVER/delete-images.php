<?php
    error_reporting(-1);
    require_once "access-control.php";
    require_once "functions.php";

    // Ladda in vår JSON data från vår fil
    
    $users = json_decode(file_get_contents("DATABAS/users.json"), true);
    $posts = json_decode(file_get_contents("DATABAS/posts.json"), true);
    
    // $users = loadJSON("DATABAS/users.json");
    // $posts = loadJSON("DATABAS/posts.json");

    // Vilken HTTP metod vi tog emot plus CONTENT TYPE
    $method = $_SERVER["REQUEST_METHOD"];
    $contentType = $_SERVER["CONTENT_TYPE"];
    $server = $_SERVER;

    // Hämta ut det som skickades till vår server
    // Måste användas vid alla metoder förutom GET
    $data = file_get_contents("php://input");
    $requestData = json_decode($data, true);

    // Tar bort bild från mapp plus databas

    // Tar emot { id } och sedan raderar en användare baserat på `id`
    // Skickar tillbaka { id }
    if ($method === "DELETE") {
        // Kontrollera att vi har den datan vi behöver
        if (!isset($requestData["postID"], $requestData["userID"])) {
            send(
                [
                    "code" => 1,
                    "message" => "Missing `id` of request body"
                ],
                400
            );
        }

        $postID = $requestData["postID"];
        $userID = $requestData["userID"];

        
        // Radera en bild från image-mappen
        $image_url = $posts["posts"][$postID]["image_url"];
        $http_host = $_SERVER["HTTP_HOST"];
        inspect($image_url);
        // $image_url = "http://localhost:7000/IMAGES/jEffrey.png";
        // $directory = str_replace("http://$http_host/SERVER", "", $image_url);

        // inspect($_SERVER);

        // inspect($directory);
        // unlink($directory);

        // Raderar en post 
        // unset($posts["posts"][$postID]);


       

        // Uppdatera filen
        // saveJSON("posts.json", $posts);
        // send(["id" => $id]);

    } else {
        $json = json_encode(["message" => "Method Not Allowed"]);      
        send($json, 405);
    }

?>