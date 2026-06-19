<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$id = getenv('ID');
$pass = getenv('PASS');

$DB_host = getenv('DB_HOST');
$DB_user = getenv('DB_USER');
$DB_pass = getenv('DB_PASS');
$DB_name = getenv('DB_NAME');

$clientId = getenv('CLIENT_ID');
$clientSecret = getenv('CLIENT_SECRET');
$redirectUri = getenv('REDIRECT_URI');
$botToken = getenv('BOT_TOKEN');