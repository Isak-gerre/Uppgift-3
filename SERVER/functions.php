<?php


function send($data, $statusCode = 200)
{
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $json = json_encode($data);
    echo $json;
    exit();
}
function loadJSON($filename)
{
    if (!file_exists("DATABAS/$filename")) {
        return false;
    }
    $data = json_decode(file_get_contents($filename), true);
    return $data;
}
function saveJSON($filename, $data)
{
    file_put_contents("DATABAS/$filename", json_encode($data, JSON_PRETTY_PRINT));
    return true;
}

function inspect($variable)
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
}


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

function getUsersByIDs($arrayOfIDs)
{
    $users = loadJSON("DATABAS/users.json");
    $newArray = [];
    foreach ($users as $key => $user) {
        foreach ($arrayOfIDs as $id) {
            if ($user["id"] == $id) {
                $newArray[] = $users[$key];
            }
        }
    }
    return $newArray;
}
function getUsersByLimit($limit)
{
    $users = loadJSON("DATABAS/users.json");
    array_splice($users, intval($limit));

    return $users;
}

function logToLog($message, $error = "INFO")
{
    date_default_timezone_set('Europe/Stockholm');
    $date = date('y-m-d H:i:s');
    $output = "[$date][$error] $message \n";
    file_put_contents("log.txt", $output, FILE_APPEND);
}

function getUserImages($id)
{
    $user = getUsersByIDs([$id]);
    $images = $user["images"];
    return $images;
}
