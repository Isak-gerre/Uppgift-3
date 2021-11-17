<!-- 
    POST
    •Kunna skapa en entitet. Ni ska kontrollera att alla fält existerar och inte är tomma. Skulle något saknas ska ni svara med något relevant meddelande så att användaren av ert API förstår vad som gått fel. Glöm inte att tänka på eventuella relationer som måste inkluderas i användarens förfrågan.
-->
<?php
$method = $_SERVER["REQUEST_METHOD"];

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");

// Den sk. preflight förfrågan ("får jag anropa dig")
if ($method === "OPTIONS") {
    // Tillåt alla (origins) och alla headers
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Headers: content-type ");
    exit();
}

// Alla är vällkommna
if ($method === "GET") {
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

    $title = $_FILES["title"];

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
    // Hämta ut filändelsen (och gör om till gemener)
    $ext = strtolower($info["extension"]);

    // Konvertera från int (siffra) till en sträng,
    // så vi kan slå samman dom nedan.
    $time = (string) time(); // Klockslaget i millisekunder
    // Skapa ett unikt filnamn
    $uniqueFilename = sha1("$time$filename");
    // Samma filnamn som den som laddades upp
    move_uploaded_file($tempname, "IMAGES/POSTS/$uniqueFilename.$ext");


    //Lägg till bilden i databasen
    $database = json_decode(file_get_contents("database/database.json"), true);
    $images = $database["images"];

    $newImg = [
        "id" => "312312",
        "image_url" => "http://localhost:7000/SERVER/IMAGES/POSTS/$uniqueFilename.$ext",
        "total_likes" => 0,
        "likes" => [],
        "date" => date("Y/m/d")
    ];
    $database["nextKey"] = $database["nextKey"] + 1;
    $images[] = $newImg;
    $database["images"] = $images;
    $tojson = json_encode($database, JSON_PRETTY_PRINT);
    file_put_contents("database/database.json", $tojson);
    // JSON-svar när vi testade med att skicka formuläret via JS
    header("Content-Type: application/json");
    http_response_code(200);
    exit();
}

// file_exists($filename); -> Kontrollera om en fil finns eller inte
// unlink($filename); -> Radera en fil
