<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $headers     = getallheaders();
    $auth        = $headers['Authorization'] ?? '';
    $providedKey = str_starts_with($auth, 'Bearer ') ? substr($auth, 7) : null;
    

    $stmt = $conn->prepare("SELECT id, username FROM user WHERE api_key = ? LIMIT 1");
    $stmt->bind_param("s", hash('sha256', $apiKeyRecue));
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        json(401, ['error' => 'Clé API invalide ou manquante']);
        exit;
    }
    
    // ── Récupération du body JSON ────────────────────────────
    $body = json_decode(file_get_contents('php://input'), true);

    if (!$body || !isset($body['msg'])) {
        json(400, ['error' => 'Body JSON invalide ou manquant']);
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM requetes WHERE user_id = ? AND created_at >= NOW() - INTERVAL 24 HOUR");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $stmt->close();

    if ($count >= 100) {
        json(429, ['error' => 'Limite de requêtes atteinte. (10/24h glissante)']);
    }

    $stmt = $conn->prepare("INSERT INTO requetes (`user_id`, `loser`, `message`) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $body['looser'] ?? '', $body['msg']);

    $stmt->execute();
    $stmt->close();

    send_messages(
        $body['looser'] ?? '',
        $body['msg'] . '\nCe message perdant vous à été envoyée par ' . $user['username'] 
    );
    json(200, ['response' => "Messages send !"]);
}

?>
