<!-- 
    •DELETE
    •Kunna radera en entitet baserat på ett ID. Ni ska kontrollera att ID:et dom specificerat faktiskt existerar. Skulle något gå fel ska ni svara med något relevant meddelande så att användaren av ert API förstår vad som gått fel. 
-->
<?php
    error_reporting(-1);
    require_once "access-control.php";

    // Ladda in vår JSON data från vår fil
    $users = loadJSON("database.json");

    // Vilken HTTP metod vi tog emot plus CONTENT TYPE
    $method = $_SERVER["REQUEST_METHOD"];
    $contentType = $_SERVER["CONTENT_TYPE"];

    // Hämta ut det som skickades till vår server
    // Måste användas vid alla metoder förutom GET
    $data = file_get_contents("php://input");
    $requestData = json_decode($data, true);


    // Tar emot { id } och sedan raderar en användare baserat på `id`
    // Skickar tillbaka { id }
    if ($method === "DELETE") {
        // Kontrollera att vi har den datan vi behöver
        if (!isset($requestData["id"])) {
            send(
                [
                    "code" => 1,
                    "message" => "Missing `id` of request body"
                ],
                400
            );
        }

        // TODO: kontrollera att `id` är en siffra

        $id = $requestData["id"];
        $found = false;

        foreach ($users as $index => $user) {
            if ($user["id"] == $id) {
                $found = true;
                array_splice($users, $index, 1);
                break;
            }
        }

        if ($found === false) {
            send(
                [
                    "code" => 2,
                    "message" => "The users by `id` does not exist"
                ],
                404
            );
        }

        // Uppdatera filen
        saveJson("users.json", $users);
        send(["id" => $id]);
    }
    // if ($rqMethod == "DELETE"){

    // } else {
    //     $json = json_encode(["message" => "Method Not Allowed"]);      
    //     sendJson($json, 405);
    // }
?>