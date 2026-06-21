<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = ($_POST['name'] == "" ? "Quelqu'un" : $_POST['name']);
    $count = $_POST['loser'] == "" ? "tous vous" : "te";
    $message = $name .  " a décidé de " . $count . " faire perdre :(";

    send_messages($_POST['loser'], $message);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de connexion : " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO requetes (`IP`, `user_agent`, `name`, `created_at`) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $name);

    $stmt->execute();
    $stmt->close();

    $conn->close();
    
    $message_output = "Message envoyé avec succés";
    
    include 'confirm.html';
}

?>
