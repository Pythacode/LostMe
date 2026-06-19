<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');


$key = $_GET['key'] ?? null;
$value = $_GET['value'] ?? null;

if ($key === null || $value === null) {
    http_response_code(400);
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = new mysqli($host, $userDB, $DBpass, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connexion échouée']);
    die("Échec de connexion : " . $conn->connect_error);
}

$allowedColumns = ['username', 'freeID'];
if (!in_array($key, $allowedColumns)) {
    http_response_code(400);
    echo json_encode(['error' => "Colonne non autorisée"]);
    exit;
}

// Construire la requête avec le nom de colonne sécurisé
$sql = "SELECT 1 FROM users WHERE $key = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->store_result();

http_response_code(200);
echo json_encode(['exists' => $stmt->num_rows > 0]);

$stmt->close();
$conn->close();
?>
