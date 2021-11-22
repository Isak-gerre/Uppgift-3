<?php

error_reporting(-1);
require_once "access-control.php";
require_once "functions.php";

$usersDB = loadJSON("DATABAS/users.json");
$postsDB = loadJSON("DATABAS/posts.json");
$users = $usersDB["users"];
$posts = $postsDB["posts"];

if ($method === "GET" && empty($_GET)) {
    send(getUsers());
}

if ($method === "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];
    send(getUsersById($id));
}

if ($method === "GET" && isset($_GET["ids"])) {
    $ids = $_GET["ids"];
    $array = getImageByIds($ids);
    send($array["users"], $array["errorCode"]);
}

if ($method === "GET" && isset($_GET["limit"])) {
    $limit = $_GET["limit"];
    send(getUsersByLimit($limit));
}

?>