
<?php

error_reporting(-1);
require_once "access-control.php";
require_once "functions.php";

if ($method === "GET" && empty($_GET)) {
    getImages();
}

if ($method === "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];
    getImage($id);
}

if ($method === "GET" && isset($_GET["span"])) {
    $ids = $_GET["span"];
    getImageByIds($ids);
}

if ($method === "GET" && isset($_GET["user_posts"])) {
    $user = $_GET["user_posts"];
    getImagesByUser($user);
}

?>
