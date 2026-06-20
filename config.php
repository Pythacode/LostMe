<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$id = getenv('ID');
$pass = getenv('PASS');

$DB_host = getenv('DB_HOST');
$DB_user = getenv('DB_USER');
$DB_pass = getenv('DB_PASS');
$DB_name = getenv('DB_NAME');

$clientId = getenv('CLIENT_ID');
$clientSecret = getenv('CLIENT_SECRET');
$redirectUri = getenv('REDIRECT_URI');
$botToken = getenv('BOT_TOKEN');

$conn = new mysqli($DB_host, $DB_user, $DB_pass, $DB_name);

if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Connexion BDD échouée : ' . $conn->connect_error]);
    exit;
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

function send_messages($loser, $message) {
    global $conn, $botToken;
    if ($loser == '') {

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
}