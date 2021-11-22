
<?php

error_reporting(-1);
require_once "access-control.php";
require_once "functions.php";

// Alla är vällkommna
if ($method !== "POST") {
    header("Content-Type: application/json");
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

if ($method === "POST" && isset($_FILES["image"])) {
    $usersDB = json_decode(file_get_contents("DATABAS/users.json"), true);


    $file = $_FILES["image"];
    $filename = $file["name"];
    $tempname = $file["tmp_name"];
    $size = $file["size"];
    $error = $file["error"];


    if (!isset($_POST["id"])) {
        send(
            ["message" => "'Id' has to be sent in request"],
            400
        );
        exit();
    }
    $caption = $_POST["caption"];
    $userID = $_POST["id"];
    // Kontrollera att allt gick bra med PHP
    // (https://www.php.net/manual/en/features.file-upload.errors.php)
    if ($error !== 0) {
        send(
            ["message" => "Something went wrong"],
            409
        );
        exit();
    }

    // // Filen får inte vara större än ~1MB
    if ($size > (1 * 1024 * 1024)) {
        send(
            ["message" => "The image is too large"],
            406
        );
        exit();
    }
    // Kollar om användaren finns
    if (!array_key_exists($userID, $usersDB["users"])) {
        send(
            ["message" => "The user with that ID does not exist"],
            404
        );
        exit();
    }



    // Hämta filinformation
    $info = pathinfo($filename);
    // Hämta ut filändelsen (och gör om till gemener)
    $ext = strtolower($info["extension"]);

    // Konvertera från int (siffra) till en sträng,
    // så vi kan slå samman dom nedan.
    $time = (string) time(); // Klockslaget i millisekunder
    // Skapa ett unikt filnamn
    $uniqueFilename = sha1("$time$filename");
    $uniqueID = sha1("$time$caption");
    // Samma filnamn som den som laddades upp
    move_uploaded_file($tempname, "IMAGES/POSTS/$uniqueFilename.$ext");

    // Lägg till postID i användaren. 
    $userPosts = $usersDB["users"][$userID]["posts"];
    $userPosts[] = $uniqueID;
    $usersDB["users"][$userID]["posts"] = $userPosts;
    $postIDtojson = json_encode($usersDB, JSON_PRETTY_PRINT);
    file_put_contents("DATABAS/users.json", $postIDtojson);

    //Lägg till bilden i databasen
    $database = json_decode(file_get_contents("DATABAS/posts.json"), true);
    $posts = $database["posts"];

    $newPost = [
        "id" => $uniqueID,
        "image_url" => "http://localhost:4000/IMAGES/POSTS/$uniqueFilename.$ext",
        "total_likes" => 0,
        "likes" => [],
        "date" => date("Y/m/d"),
        "caption" => ""
    ];
    $posts[$uniqueID] = $newPost;
    $database["posts"] = $posts;
    saveJSON("DATABAS/posts.json", $database);
    // JSON-svar när vi testade med att skicka formuläret via JS
    send(
        ["Post Created" => $newPost],
        201
    );
}

// file_exists($filename); -> Kontrollera om en fil finns eller inte
// unlink($filename); -> Radera en fil

?>