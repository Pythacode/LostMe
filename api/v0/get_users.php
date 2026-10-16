<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $headers     = getallheaders();
    $auth        = $headers['Authorization'] ?? '';
    $providedKey = str_starts_with($auth, 'Bearer ') ? substr($auth, 7) : null;
    

    $stmt = $conn->prepare("SELECT id, username FROM users WHERE api_key = ? LIMIT 1");
    $stmt->bind_param("s", hash('sha256', $providedKey));
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        json(401, ['error' => 'Clé API invalide ou manquante']);
        exit;
    }

    $stmt = $conn->query("SELECT username FROM users WHERE confirm = 1");

    if (!$stmt) {
        json(500, ['error' => 'Erreur SQL']);
        exit;
    }

    $users = [];
    while ($row = $stmt->fetch_assoc()) {
        $users[] = $row['username'];
    }

    $stmt->close();

    json(200, $users);
}
?>