<?php
session_start();

if (!isset($_SESSION['discord_state']) || !isset($_GET['state']) || $_GET['state'] !== $_SESSION['discord_state']) {
    die("Erreur : State invalide (CSRF). <a href='/'>Retour à l'accueil</a>");
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Vérification
if (!isset($_GET["code"])) {
    die("Code manquant");
}

$code = $_GET["code"];

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

$tokenData = json_decode($response, true);
$token = $tokenData['access_token'] ?? null;

if (!$token) {
    die("Erreur OAuth: Impossible de récupérer le token.");
}

$ch = curl_init("https://discord.com/api/users/@me");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$userResponse = curl_exec($ch);
curl_close($ch);

// 4. Décode les infos utilisateur
$userData = json_decode($userResponse, true);

if (!$userData || isset($userData['error'])) {
    die("Erreur : Impossible de récupérer les infos utilisateur.");
}

$discordId = $userData['id'];

$stmt = $conn->prepare("SELECT * FROM `users` WHERE `discordID` = ?");
$stmt->bind_param("s", $discordId);

$stmt->execute();   

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    session_regenerate_id(true);
    $_SESSION['id'] = $user["id"];
    $_SESSION['mail'] = $user["email"];
    $_SESSION['username'] = $user["username"];
    $_SESSION['admin'] = $user["admin"];
    $_SESSION['news'] = $user["news"];
} else {
    header('Location: /become-a-looser/');
}

?>