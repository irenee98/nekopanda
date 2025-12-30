<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../configuracion/db.php';
require_once __DIR__ . '/../configuracion/functions.php';
$config = require __DIR__ . '/../configuracion/config.php';
$role = $_SESSION['role'] ?? 'guest';


$_SESSION = [];
session_destroy();


header("Location: index.php");
exit;


?>