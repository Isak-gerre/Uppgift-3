<?php
// Ändra caption
// Ändra totala_likes
// Ändra likes



error_reporting(-1);
require_once "access-control.php";
require_once "functions.php";

// Ladda in vår JSON data från vår fil
$users = loadJSON("DATABAS/users.json");
$postsDB = loadJSON("DATABAS/posts.json");
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

// Updaterar Likes på en bild.

if (isset($requestData["userID"], $requestData["postID"], $requestData["removing"])) {
    $userID = $requestData["userID"];
    $postID = $requestData["postID"];
    if ($requestData["removing"] == "true") {
        $likes = $posts[$postID]["likes"];
        foreach ($likes as $key => $id) {
            if ($id == $userID) {
                array_splice($likes, intval($key), 1);
            }
        }
        $posts[$postID]["likes"] = $likes;
        $posts[$postID]["total_likes"] -= 1;
        $postsDB["posts"] = $posts;
        saveJSON("DATABAS/posts.json", $postsDB);
        send(
            ["message" => "Disliked post"],
            200
        );
    } else {
        $likes = $posts[$postID]["likes"];
        if (!in_array($userID, $likes)) {
            $likes[] = $requestData["userID"];
            $posts[$postID]["likes"] = $likes;
            $posts[$postID]["total_likes"] += 1;
            $postsDB["posts"] = $posts;
            saveJSON("DATABAS/posts.json", $postsDB);
            send(
                ["message" => "Liked post"],
                200
            );
        } else {
            send(
                ["message" => "That user already likes that post!"],
                400
            );
        }
    }
} else {
    send(
        ["message" => "Missing body"],
        400
    );
}
