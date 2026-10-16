<?php

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";

if (isset($_SESSION['id'])) {

    $plainKey = bin2hex(random_bytes(32));
    $hash = hash('sha256', $plainKey);
    
    $stmt = $conn->prepare("UPDATE users SET api_key = ? WHERE id = ?");
    $stmt->bind_param("si", $hash, $_SESSION['id']);
    $stmt->execute();

    json(200, ["api_key" => $plainKey]);


    exit;
} else {
    json(401, ["error" => "Connecte toi !!!"]);
}
?>
