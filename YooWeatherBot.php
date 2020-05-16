<?php
    require 'api_key.php';
    $path = $bot_path;
    $apiKey = $api_key;
    $update = json_decode(file_get_contents("php://input"), TRUE);
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];

    if (strpos($message, "/") !== false) {
        file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=Type the name of Country or City.");
    }else {
        $location = $message;
        $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?q=" . $location . "&lang=en&units=metric&APPID=" . $apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        $data = json_decode($response);
        if ($data->cod == 200) {
            file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=Here's the weather in " . $location . ": " . ucwords($data->weather[0]->description) . ",Humidity:" . $data->main->humidity . ",Wind:" . $data->wind->speed
            );
        } else {
            file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=City not found! Try again.");
        }
    }