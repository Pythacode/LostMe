<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Vérification
if (!isset($_GET["code"])) {
    die("Code manquant");
}

$code = $_GET["code"];

// 🔁 Échange du code contre un token
$data = [
    "client_id" => $clientId,
    "client_secret" => $clientSecret,
    "grant_type" => "authorization_code",
    "code" => $code,
    "redirect_uri" => $redirectUri
];

$ch = curl_init("https://discord.com/api/oauth2/token");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$token = json_decode($response, true)["access_token"] ?? null;
if (!$token) {
    die("Erreur OAuth");
}

$ch = curl_init("https://discord.com/api/users/@me");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$user = json_decode(curl_exec($ch), true);
curl_close($ch);

// 🎉 Données récupérées
$discordId = $user["id"];
$username = $user["username"];
$globalName = $user["global_name"] ?? "";
$state = $_GET['state'];

$msg = "Nouvel utilisateur !\n";
foreach ($user as $key => $value) {
    $msg .= "$key -> $value\n";
}

echo msg;

$conn = new mysqli($DB_host, $DB_user, $DB_pass, $DB_name);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

$url = "https://discord.com/api/v10/users/@me/channels";

$data = [
    "recipient_id" => $discordId
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
curl_close($ch);

$channel = json_decode($response, true);
$channelId = $channel["id"];


$stmt = $conn->prepare("UPDATE `users` SET confirm = 1, discordID = ?, channelID=? WHERE id = ?");
$stmt->bind_param("ssi", $discordId, $channelId, $state);
$stmt->execute();

$stmt->close();
$conn->close();

$message = "Become a loser\n\nVotre compte à été crée, vous recevrez les messages perdants.";

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

$message = 'Votre inscription à été prise en compte, cependant, en raison des restrictions de Discord, vous devrez peut être installer mon bot via le lien ci-dessous avant qu\'il puisse vous faire perdre.<br>Si vous avez recus un message, inutile de suivre ce lien.';
$discord_link_mp = "https://discord.com/oauth2/authorize?client_id=1060667414225895474";

$link = '/';
$button = 'Retour à l\'accueil';

include 'result.html';
