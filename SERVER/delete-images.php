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

    // Tar emot { id } och sedan raderar en användare baserat på `id`
    // Skickar tillbaka { id }
    if ($method === "DELETE") {
        // Värden som skickat med metoden
        $postID = $requestData["postID"];
        $userID = $requestData["userID"];

        // Kontrollera att vi har den datan vi behöver
        if (isset($requestData["postID"], $requestData["userID"])) {
            // Kontrollerar om nyckeln finns
            if (array_key_exists($postID, $posts["posts"])){
                // Radera en bild från image-mappen
                $image_url = $posts["posts"][$postID]["image_url"];
                $http_host = $_SERVER["HTTP_HOST"];
                $directory = str_replace("http://$http_host/", "", $image_url);
                unlink($directory);

                // Raderar postens ID från avändarens array av posts
                $arrayPosts = $users["users"][$userID]["posts"];
                foreach($arrayPosts as $index => $post){
                    if($post == $postID){
                        array_slice($users["users"][$userID]["posts"], $index, 1);
                        break;
                    }
                }
                
                // Raderar en post från databasen
                unset($posts["posts"][$postID]);
                
                // Uppdaterar filen
                saveJSON("users.json", $users);
                saveJSON("posts.json", $posts);
                // send(["id" => $id]);

            } else {
                $message = [
                    "code" => 3,
                    "message" => "Id dosn¨t exist"
                ];
                send($message, 404);
            }
        } else {
            $message = [
                "code" => 2, 
                "message" => "Missing `id` of request body"
            ];
            send($message, 400);
        }

    } else {
        $message = [
            "code" => 1,
            "message" => "Method Not Allowed"
        ];    
        send($message, 405);
    }
?>