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

function getUsers()
{
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

function getImages($id = "All")
{
    $post = loadJSON("DATABAS/posts.json");
    if (isset($id)) {
        echo 1;
        send($post["posts"]);
    } else if ($id === "All") {
        send($post["posts"]);
    }
}

function getImagesByUser()
{
    $users = loadJSON("DATABAS/users.json");
    foreach ($users as $user) {
        foreach ($user["posts"] as $post) {
            $post;
        }
    }
    return $users;
}

// Returnerar en uppdaterad array av användarna 
function removeFromFollower($users, $userID, $following, $followers)
{
    $arrayOfUsers = $users[$userID][$following];

    foreach ($arrayOfUsers as $userid) {
        $index = array_search($userid, $arrayOfUsers);
        array_splice($users["$userid"][$followers], $index, 1);
    }

    return $users;
}
// Returnerar en uppdaterad array av användarna
function removeFromFollowing($users, $userID, $following, $followers)
{
    $arrayOfUsers = $users[$userID][$followers];

    foreach ($arrayOfUsers as $userid) {
        $index = array_search($userid, $arrayOfUsers);
        array_splice($users["$userid"][$following], $index, 1);
    }

    return $users;
}
// Returnerar en uppdaterad array av Posts
function removeLikes($posts, $userID)
{

    foreach ($posts as $key => $post) {
        foreach ($post["likes"] as $index => $id) {
            if ($userID == $id) {
                $likes = $post["likes"];
                array_splice($likes, $index, 1);
                $post["likes"] = $likes;

                $post["total_likes"] -= 1;
                $posts[$key] = $post;
            }
        }
    }

    return $posts;
}
// Kollar om nyckeln redan finns
function alreadyTaken($array, $key, $newVariable)
{
    $found = false;
    foreach ($array as $arritem) {
        if ($arritem[$key] === $newVariable) {
            $found = true;
            break;
        }
        return $found;
    }
}
