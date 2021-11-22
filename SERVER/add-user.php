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

if ($method === "POST") {
    if (
        !isset($_POST["location"], $_POST["email"], $_POST["username"], $_POST["password"], $_POST["birthday"], $_POST["bio"], $_FILES["profile_picture"])
        || empty($_POST["location"]) || empty($_POST["email"]) || empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["birthday"]) || empty($_POST["bio"]) || empty($_FILES["profile_picture"])
    ) {
        send(
            ["message" => "All fields has to be filled and has to contain something"],
            400
        );
        exit();
    }
    $profileImage = $_FILES["profile_picture"];
    $filename = $profileImage["name"];
    $tempname = $profileImage["tmp_name"];
    $size = $profileImage["size"];
    $error = $profileImage["error"];
    
    $location = $_POST["location"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $birthday = $_POST["birthday"];
    $bio = $_POST["bio"];
    
    $database = json_decode(file_get_contents("DATABAS/users.json"), true);
    $users = $database["users"];
    $userID = $database["nextID"];
    
    $alreadyTakenEmail = alreadyTaken($users, "email", $email);
    $alreadyTakenUsername = alreadyTaken($users, "username", $username);
    
    // Kollar om email redan är taget
    if ($alreadyTakenEmail) {
        $message["message"] = "Email already in use";
        send($message, 400);
    }
    
    if ($alreadyTakenUsername) {
        $message["message"] = "Username already in use";
        send($message, 400);
    }



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
    move_uploaded_file($tempname, "IMAGES/PROFILE/$uniqueFilename.$ext");


    //Lägg till bilden i databasen

    $newUser = [
        "id" => "$userID",
        "username" => $username,
        "email" => $email,
        "password" => $password,
        "bio" => $bio,
        "profile_picture" => "http://localhost:7000/IMAGES/PROFILE/$uniqueFilename.$ext",
        "followers" => [],
        "following" => [],
        "posts" => [],
        "birthday" => $birthday,
    ];
    $users[$database["nextID"]] = $newUser;
    $database["nextID"] = $database["nextID"] + 1;
    $database["users"] = $users;
    saveJSON("DATABAS/users.json", $database);
    // JSON-svar när vi testade med att skicka formuläret via JS
    send(
        ["User created" => $newUser],
        201
    );
    exit();
}
