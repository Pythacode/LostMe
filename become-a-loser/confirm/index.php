<?php

function fatal_error($message) {
    $link = '/become-a-loser/confirm';
    $button = 'Revenir à la page de confirmation';
    include 'result.html';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    include 'index.html';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once __DIR__ . '/../../../config.php';

    $conn = new mysqli($host, $userDB, $DBpass, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de connexion : " . $conn->connect_error);
    }

    // Récupérer le nom d'utilisateur depuis le formulaire
    $username = $_POST['username'];

    // Vérifier si le nom existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        fatal_error("Ce nom d'utilisateur est inconus.");
    }

    $code = $_POST['code'];

    // Vérifier si la clé d'API existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE verifCode = ? AND username = ?");
    $stmt->bind_param("ss", $code, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        fatal_error("Code inconu.");
    } else {      
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $stmt->close();
        $stmt = $conn->prepare("UPDATE `users` SET confirm=1 WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    $message = "Become a loser\n\nVotre compte à été crée, vous recevrez les SMS perdants.";

    $url = "https://smsapi.free-mobile.fr/sendmsg?user=" . $id . "&pass=" . $pass . "&msg=" . urlencode($message);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $message = 'Votre inscription à été finalisé.';
    $link = '/';
    $button = 'Retour à l\'accueil';

    include 'result.html';
}


?>