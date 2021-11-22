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

// Data som skickas med metoden (POST)
$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

// 1. Kollar om metoden stämmer
if ($method !== "POST") {
    $message = ["message" => "Method Not Allowed"];
    send($message, 405);
}

// 2. Kollar om Content-TYPE = Multipart/form-data;
if ($contentType !== "multipart/form-data; boundary=X-INSOMNIA-BOUNDARY"){
    $message = ["message" => "The API only accepts multipart/form-data"];
    send($message, 404);
}

// 3. Kollar om det finns en fil skickad
if (isset($_FILES["profile-picture"])) {

    $file = $_FILES["profile-picture"];
    $filename = $file["name"];
    $tempname = $file["tmp_name"];
    $size = $file["size"];
    $error = $file["error"];
    
    $userID = $_POST["id"];
    

    // Kontrollera att allt gick bra med PHP
    // (https://www.php.net/manual/en/features.file-upload.errors.php)
    if ($error !== 0) {
        http_response_code(402);
        exit();
    }

    // Filen får inte vara större än ~1MB
    if ($size > (1 * 1024 * 1024)) {
        http_response_code(400);
        exit();
    }
    

    // Hämta filinformation
    $info = pathinfo($filename);
    inspect($info);
    // Hämta ut filändelsen (och gör om till gemener)
    $ext = strtolower($info["extension"]);

    // Konvertera från int (siffra) till en sträng,
    // så vi kan slå samman dom nedan.
    $time = (string) time(); // Klockslaget i millisekunder
    
    // Skapa ett unikt filnamn
    $uniqueFilename = sha1("$time$filename");
    
    // Samma filnamn som den som laddades upp
    move_uploaded_file($tempname, "IMAGES/PROFILE/$uniqueFilename.$ext");
    
    // Tar bort den tidigare bilden ur USER-databas
    $profilePicture = $users[$userID]["profile-picture"];
    // Hämtar vilken localhost siffra som  använts vid öppnandet av terminalen
    $http_host = $_SERVER["HTTP_HOST"];
    $directory = str_replace("http://$http_host/", "",  $profilePicture);
    unlink($directory);
    
    // Ändrar personens profile-picture i databasen, 
    // till den nya
    $users[$userID]["profile-picture"] = "http://localhost:7000/IMAGES/PROFILE/$uniqueFilename.$ext";

    // Sparar i databasen
    $usersDB["users"] = $users;
    saveJSON("DATABAS/users.json", $usersDB);

    // Meddelande om att allt gick bra
    $message = [
        "message" => "You succeded to change your profile"
    ];
    send($message);
}
// file_exists($filename); -> Kontrollera om en fil finns eller inte
?>