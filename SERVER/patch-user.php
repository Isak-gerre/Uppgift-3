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
if (!isset($requestData["userID"])) {
    $message = [
        "code" => 3,
        "message" => "Who are you?"
    ];
    send($message, 404);
}

$userID = $requestData["userID"];

// Kollar vilka nycklar som är ifyllda för att ändra dessa
$username = $requestData["username"];
if (isset($username)) {
    $alreadyTaken = alreadyTaken($users, "username", $username);

    if (!$alreadyTaken) {
        $users[$userID]["username"] = $requestData["username"];
    } else {
        $message = [
            "code" => 4,
            "message" => "Username already taken"
        ];
        send($message, 404);
    }
}

$email = $requestData["username"];
if (isset($email)) {
    $alreadyTaken = alreadyTaken($users, "email", $email);

    if (!$alreadyTaken) {
        $users[$userID]["email"] = $requestData["email"];
    } else {
        $message = [
            "code" => 4,
            "message" => "Email already taken"
        ];
        send($message, 404);
    }
}

    // if (isset($requestData["password"])) {

    //     $users[$userID][] = $requestData["username"];
    // }

    // if (isset($requestData["location"])) {
    //     $users[$userID]["location"] = $requestData["username"];
    // }

    // if (isset($requestData["birthday"])) {
    //     $users[$userID] = $requestData["username"];
    // }

    // if (isset($requestData["profile-picture"])) {
    //     $users[$userID] = $requestData["username"];
    // }

    // if (isset($requestData["bio"])) {
    //     $users[$userID] = $requestData["username"];
    // }