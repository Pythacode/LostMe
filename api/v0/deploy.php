<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";

define('SECRET', $config['deploy-key']);
define('REPO_PATH', $_SERVER['DOCUMENT_ROOT']);
define('BRANCH', 'main');

// Vérification signature GitHub
$payload = file_get_contents('php://input');
$signature = 'sha256=' . hash_hmac('sha256', $payload, SECRET);
if (!hash_equals($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '', $signature)) {
    http_response_code(403);
    die("Unauthorized");
}

// Vérification branche
$data = json_decode($payload, true);
if ($data['ref'] !== 'refs/heads/' . BRANCH) {
    die("Mauvaise branche");
}

// Pull
$output = shell_exec('cd ' . REPO_PATH . ' && git pull origin ' . BRANCH . ' 2>&1');
echo "<pre>$output</pre>";