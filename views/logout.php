<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$user = new User($db->connect());
$user->logout();

header('Location: login.php');
exit;
