<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile(__DIR__ . '/config.yaml');

$id = $config['id'];
$pass = $config['pass'];
$host = $config['host'];
$userDB = $config['userDB'];
$DBpass = $config['DBpass'];
$dbname = $config['dbname'];
$clientId = $config['clientId'];
$clientSecret = $config['clientSecret'];
$redirectUri = $config['redirectUri'];
$botToken = $config['botToken'];
