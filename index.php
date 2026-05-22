<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

$conn = new mysqli($host, $userDB, $DBpass, $dbname);


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $index = mt_rand(5, 15);
    
    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/res/files/names.txt', 'r');
 
    $count = 1;
    $name = null;

    while (($ligne = fgets($file)) !== false) {
        if ($count == $index) {
            $name = trim($ligne);
            break;
        }
        $count++;
    }

    fclose($file);

    $result = $conn->query("SELECT username FROM users");

    include './index.html';
}

function send_message(array $row, string $message, string $botToken) {
    if ($row['method'] == 'free') {
            $url = "https://smsapi.free-mobile.fr/sendmsg?user=" . $row['freeID'] . "&pass=" . $row['APIkey'] . "&msg=" . urlencode($message);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        }
        if ($row['method'] == 'discord') {
            
            $channelId = $row["channelID"];

            $url = "https://discord.com/api/v10/channels/$channelId/messages";

            $data = [
                "content" => $message
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bot $botToken",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);                
        }

}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = ($_POST['name'] == "" ? "Quelqu'un" : $_POST['name']);
    $count = $_POST['loser'] == "" ? "tous vous" : "te";
    $message = $name .  " a décidé de " . $count . " faire perdre :(";

    if ($_POST['loser'] == '') {

        $stmt = $conn->prepare("SELECT `confirm`, `freeID`, `APIkey`, `method`, `channelID` FROM `users`");
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as $row) {
            send_message($row, $message, $botToken);
        }

    } else {
        $stmt = $conn->prepare("SELECT `confirm`, `freeID`, `APIkey`, `method`, `channelID` FROM `users` WHERE `username` = ?");
        $stmt->bind_param("s", $_POST['loser']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        send_message($row, $message, $botToken);
        
    }

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de connexion : " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO requetes (`IP`, `user-agent`, `name`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $name);

    $stmt->execute();
    $stmt->close();

    $conn->close();

    $message_output = "Message envoyé avec succés";

    include 'confirm.html';
}


?>
