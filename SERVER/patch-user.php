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

// 1. Kollar om det är rätt metod
if ($method !== "PATCH") {
    $message = [
        "code" => 1,
        "message" => "Method Not Allowed"
    ];
    send($message, 405);
}

// 2. Kollar om Content-TYPE = JSON
if ($contentType !== "application/json") {
    $message = [
        "code" => 2,
        "message" => "The API only accepts JSON"
    ];
    send($message, 404);
}


// 3. Kollar vilket id som skickats med 
if (!isset($requestData["userID"]) || !array_key_exists($requestData["userID"], $users)) {
    $message = [
        "code" => 3,
        "id" => array_key_exists($requestData["userID"], $users),
        "message" => "Who are you?"
    ];
    send($message, 404);
}

$userID = $requestData["userID"];

$executing = true;
$message = [];

// Om USERNAME nyckeln finns
if (isset($requestData["username"])) {
    $username = $requestData["username"];
    $alreadyTaken = alreadyTaken($users, "username", $username);
    
    // Kollar så att användarnamnet inte är upptaget
    if ($alreadyTaken) {
        $message["username"] = "Username already taken";
        send($message, 404);
        $executing = false;
    } 
    // Kollar så att användarnamnet är längre än 2 bokstäver
    if (strlen($username) <= 2){
        $message["username"] = "Username has to be more than 2 characters";
        $executing = false;
    }
    // Om inget fel uppjagats så ändra vi nyckeln
    if($executing){
        $users[$userID]["username"] = $requestData["username"];
        $message["username"] = "You succeded changing your username";

    }
}

// Om EMAIL nyckeln finns
if (isset($requestData["email"])) {
    $email = $requestData["email"];
    $alreadyTaken = alreadyTaken($users, "email", $email);
    
    // Kollar om email redan är taget
    if ($alreadyTaken) {
        $message["email"] = "Email already taken";
        $executing = false;
    }
    // Kollar så att emailen innehåller "@" och "."
    if(strpos($email, "@") === false && strpos($email, ".") === false){
        $message["email"] = "Email has to contain ''@'' and ''.''";
        $executing = false;
    }
    
    if($executing){
        $users[$userID]["email"] = $requestData["email"];
        $message["email"] = "You succeded changing your email";

    }
}
 
// Tänker att man inte ändrar lösenord när man vill ändra
// i sin profil, det kräver mer säkerhet,
// slussas iväg till en patch-passworD??!
if (isset($requestData["password"])) {
    $users[$userID]["password"] = $requestData["password"];
    $usersDB["users"] = $users;
    saveJSON("DATABAS/users.json", $usersDB);
}

// Om LOCATION är ifyllt
if (isset($requestData["location"])) {
    if($executing){
        $users[$userID]["location"] = $requestData["location"];
        $message["location"] = "You succeded changing your location";
    } 
}

// Om BIRTHDAY är ifyllt
if (isset($requestData["birthday"])) {
    $birthday = $requestData["birthday"];

    $birthdayInteger = intval($birthday);
    // Kollar så att det är en siffra 
    inspect($birthdayInteger);
    if(!is_int($birthdayInteger) || $birthdayInteger == 1 || $birthdayInteger == 0){
        $message["birthday"] = "It has to be an integer";
        $executing = false;
    }
    // Kollar så att det är ett rimligt år
    if($birthdayInteger < 1850 || $birthdayInteger > 2002){
        $message["birthday"] = "Insert a valid birthday";
        $executing = false;
    }

    if($executing){
        $users[$userID]["birthday"] = $requestData["birthday"];
        $message["birthday"] = "You succeded changing your birthday";
    }
}

// Detta blir väl mer att ladda upp en ny bild
// snarare än att ändra den??!
// if (isset($requestData["profile-picture"])) {
//     $users[$userID]["profile-picture"] = $requestData["profile-picture"];
//     $usersDB["users"] = $users;
//     saveJSON("DATABAS/users.json", $usersDB);
// }

if (isset($requestData["bio"])) {
    if($executing){
        $users[$userID]["bio"] = $requestData["bio"];
    }
}

// Om inte executing har ändrats till FALSE
// kommer den att utföra ändringarna
// annars skickar den alla felmeddelanden som kunnat uppstå 
if($executing) {
    $usersDB["users"] = $users;
    saveJSON("DATABAS/users.json", $usersDB);
    send($message, 404);
} else {
    send($message);
}