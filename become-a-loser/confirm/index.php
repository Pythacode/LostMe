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

    require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

    

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
    
    $message = "Become a loser\n\nVotre compte à été crée, vous recevrez les messages perdants.";
    
    send_messages($username, $message);
    
    $message = 'Votre inscription à été finalisé.';
    $link = '/';
    $button = 'Retour à l\'accueil';
    
    $conn->close();
    include 'result.html';
}

?>