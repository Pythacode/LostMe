<?php

function fatal_error($message) {
    $link = '/become-a-loser/';
    $button = 'Revenir à la page d\'inscription';
    include 'result.html';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    include 'index.html';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

    $conn = new mysqli($host, $userDB, $DBpass, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de connexion : " . $conn->connect_error);
    }

    // Récupérer le nom d'utilisateur depuis le formulaire
    $username = $_POST['username'];
    $method = $_POST['method'] ?? '';

    // Vérifier si le nom existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        fatal_error("Ce nom d'utilisateur est déjà pris.");
    }

    $freeID = $_POST['freeID'];

    if ($method === 'free') {

        // Vérifier si la clé d'API existe déjà
        $stmt = $conn->prepare("SELECT id FROM users WHERE freeID = ?");
        $stmt->bind_param("s", $freeID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            fatal_error("Cet utilisateur Free est déjà utilisé.");
        }


        $confirmCode = mt_rand(1000, max: 9999);
    }

    $confirmed = 0;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO `users`(`username`, `password`, `email`, `confirm`, `verifCode`, `freeID`, `APIkey`, `method`) VALUES  (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiisss", $username, $password, $_POST['mail'], $confirmed, $confirmCode, $freeID, $_POST['APIkey'], $method);

    $stmt->execute();
    $lastId = $conn->insert_id;
    $stmt->close();
    $conn->close();

    if ($method == 'free') {

        $message = "Become a loser\n\nVotre code de confirmation pour finaliser la création de votre compte est " . $confirmCode;

        $url = "https://smsapi.free-mobile.fr/sendmsg?user=" . $id . "&pass=" . $pass . "&msg=" . urlencode($message);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $message = 'Un code de confirmation vous à été envoyer par SMS.';
        $link = '/become-a-loser/confirm/';
        $button = 'Finaliser mon inscription';

        include 'result.html';

    } elseif ($method == 'discord') {

        $username = $_GET['username'] ?? '';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $domain = $protocol . "://" . $_SERVER['HTTP_HOST'];

        $discordOAuthUrl = "https://discord.com/oauth2/authorize" .
            "?client_id=1060667414225895474" .
            "&redirect_uri=" . urlencode($domain . "/become-a-loser/discord-calback.php") .
            "&response_type=code" .
            "&scope=identify" .
            "&state=$lastId";

        header("Location: $discordOAuthUrl");
        exit;

    }
}

?>