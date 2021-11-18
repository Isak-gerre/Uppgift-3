<?php

// Skickar data
function send($data, $statusCode = 200)
{
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $json = json_encode($data);
    echo $json;
    exit();
}
// Laddar data
function loadJSON($filename)
{
    if (!file_exists($filename)) {
        return false;
    }
    $data = json_decode(file_get_contents($filename), true);
    return $data;
}
// Sparar data
function saveJSON($filename, $data)
{
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
    return true;
}
// Inspekterar en variabel
function inspect($variable)
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
}
// Returnerar näst kommande högsta ID:t
function nextHighestId($filename)
{
    $users = loadJSON($filename);
    $highestId = 0;
    foreach ($users as $key => $user) {
        if ($user["id"] > $highestId) {
            $highestId = $user["id"];
        }
    }
    return $highestId + 1;
}
// Returnerar en array av en eller flera användare
function getUsersByIDs($arrayOfIDs)
{
    $users = loadJSON("DATABAS/users.json");
    $newArray = [];
    foreach ($users["users"] as $key => $user) {
        foreach ($arrayOfIDs as $id) {
            if ($user["id"] == $id) {
                $newArray[] = $users["users"][$key];
            }
        }
    }
    return $newArray;
}

function getUsers(){
    $users = loadJSON("DATABAS/users.json");
    var_dump($users["users"]);
    return $users["users"];
}

// Returnerar antalet användare efter argumentet(antal) du skickat med
function getUsersByLimit($limit)
{
    $users = loadJSON("DATABAS/users.json");
    array_splice($users, intval($limit));

    return $users;
}
// Sparar info i en text fil för att kunna notera vad som skett
function logToLog($message, $error = "INFO")
{
    date_default_timezone_set('Europe/Stockholm');
    $date = date('y-m-d H:i:s');
    $output = "[$date][$error] $message \n";
    file_put_contents("log.txt", $output, FILE_APPEND);
}
// Returnerar en array av bild ID:n
function getUserPosts($id)
{
    $user = getUsersByIDs([$id]);
    $posts = $user[0]["posts"];
    return $posts;
}
// Returnerar all informtion kring all bilder

function getImages($id = "All"){
    echo "HEj";
    $post = loadJSON("DATABAS/posts.json");
    if(isset($id)){
        echo 1;
        echo "<pre>";
        var_dump($post["posts"]);
        echo "</pre>";
    }
    else if($id === "All"){
        echo "<pre>";
        var_dump($post["posts"]);
        echo "</pre>";
    }
}

function getImagesByUser(){
    $users = loadJSON("DATABAS/users.json");
    foreach($users as $user){
        foreach($user["posts"] as $post){
            $post;
        }
    }
    return $users;
}
