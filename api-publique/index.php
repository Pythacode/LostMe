<?php

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";

if (isset($_SESSION['id'])) {
    // Has Log
    require_once 'dashboard.html';
    exit;
} else {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $stmt->bind_param("s", $username);

        $stmt->execute();   

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            if (password_verify($password, $user["password"])) {

                session_regenerate_id(true);
                $_SESSION['id'] = $user["id"];
                $_SESSION['mail'] = $user["email"];
                $_SESSION['username'] = $user["username"];
                $_SESSION['admin'] = $user["admin"];
                $_SESSION['news'] = $user["news"];

                header('Location: /api-publique/');            

                exit;

            } else  {
                $error = "Mot de passe incorrect";
                require_once 'login.html';
                exit;
            }
        } else {
            $error = "Nom d'utilisateur incorrect";
            require_once 'login.html';
            exit;;
        }

    } else {
        $_SESSION['discord_state'] = bin2hex(random_bytes(16));
        $state = $_SESSION['discord_state'];
        $redirectUri = urlencode("https://lostme.nathanaelle.org/api-publique/discord-callback.php");
        $scopes = urlencode("identify");
        $discordUri = "https://discord.com/oauth2/authorize?client_id=" . $clientId . "&redirect_uri=" . $redirectUri . "&response_type=code&scope=" . $scopes . "&state=" . $state;
        require_once 'login.html';
    }
}
?>
