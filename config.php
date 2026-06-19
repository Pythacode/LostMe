<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$id = getenv('ID');
$pass = getenv('PASS');

$id = getenv('ID');
$pass = getenv('PASS');
$host = getenv('HOST');
$userDB = getenv('DB_USER');
$DBpass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$clientId = getenv('CLIENT_ID');
$clientSecret = getenv('CLIENT_SECRET');
$redirectUri = getenv('REDIRECT_URI');
$botToken = getenv('BOT_TOKEN');