
<?php

error_reporting(-1);
require_once "access-control.php";
require_once "functions.php";

if ($method === "GET" && empty($_GET)) {
    getImages();
}

if ($method === "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];
<<<<<<< Updated upstream
    var_dump(is_numeric(1));
    if (!is_numeric(($id))) {
        echo "error";
        exit();
    }
    getImages($id);
=======
    getImage($id);
>>>>>>> Stashed changes
}

?>
