<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function json(int $code, array $data): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $headers     = getallheaders();
    $auth        = $headers['Authorization'] ?? '';
    $providedKey = str_starts_with($auth, 'Bearer ') ? substr($auth, 7) : null;
    $validKey    = $_ENV['API_KEY'] ?? '';

    if (!$providedKey || !hash_equals($validKey, $providedKey)) {
        json(401, ['error' => 'Clé API invalide ou manquante']);
        exit;
    }
    
    // ── Récupération du body JSON ────────────────────────────
    $body = json_decode(file_get_contents('php://input'), true);

    if (!$body || !isset($body['msg'])) {
        json(400, ['error' => 'Body JSON invalide ou manquant']);
        exit;
    }

    send_messages(
        $body['looser'] ?? '',
        $body['msg']
    );
    json(200, ['response' => "Messge.s send !"]);
}

?>