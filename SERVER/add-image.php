<!-- 
    POST
    •Kunna skapa en entitet. Ni ska kontrollera att alla fält existerar och inte är tomma. Skulle något saknas ska ni svara med något relevant meddelande så att användaren av ert API förstår vad som gått fel. Glöm inte att tänka på eventuella relationer som måste inkluderas i användarens förfrågan.
-->
<?php

error_reporting(-1);
require_once "access-control.php";

// Alla är vällkommna
if ($method !== "POST") {
    header("Content-Type: application/json");
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

if ($method === "POST" && isset($_FILES["image"])) {
    $file = $_FILES["image"];
    $filename = $file["name"];
    $tempname = $file["tmp_name"];
    $size = $file["size"];
    $error = $file["error"];

    $caption = $_POST["caption"];
    $userID = $_POST["id"];


    // Kontrollera att allt gick bra med PHP
    // (https://www.php.net/manual/en/features.file-upload.errors.php)
    if ($error !== 0) {
        http_response_code(402);
        exit();
    }

    // // Filen får inte vara större än ~1MB
    // if ($size > (1 * 1024 * 1024)) {
    //     http_response_code(400);
    //     exit();
    // }


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
    $usersDB = json_decode(file_get_contents("DATABAS/users.json"), true);
    var_dump($usersDB);
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
        "image_url" => "http://localhost:7000/SERVER/IMAGES/POSTS/$uniqueFilename.$ext",
        "total_likes" => 0,
        "likes" => [],
        "date" => date("Y/m/d")
    ];
    $posts[$uniqueID] = $newPost;
    $database["posts"] = $posts;
    $tojson = json_encode($database, JSON_PRETTY_PRINT);
    file_put_contents("DATABAS/posts.json", $tojson);
    // JSON-svar när vi testade med att skicka formuläret via JS
    header("Content-Type: application/json");
    http_response_code(200);
    exit();
}

// file_exists($filename); -> Kontrollera om en fil finns eller inte
// unlink($filename); -> Radera en fil

?>