
<?php

error_reporting(-1);
require_once "access-control.php";
require_once "functions.php";



if ($method !== "GET") {
    send(
        ["Error" => "Method not allowed"],
        405
    );
}

if ($method === "GET" && empty($_GET)) {
    send(getImages());
}

if ($method === "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];
    send(getImage($id));
}

if ($method === "GET" && isset($_GET["ids"])) {
    $ids = $_GET["ids"];
    send(getImageByIds($ids));
}

if ($method === "GET" && isset($_GET["user_posts"])) {
    $user = $_GET["user_posts"];
    send(getImagesByUser($user));
}

if ($method === "GET" && isset($_GET["limit"])) {
    $limit = $_GET["limit"];
    send(getImagesByLimit($limit));
}


?>
