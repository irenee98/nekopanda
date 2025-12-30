<?php
function flash($name, $message = null) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if ($message === null) {
        if (isset($_SESSION['flash'][$name])) {
            $m = $_SESSION['flash'][$name];
            unset($_SESSION['flash'][$name]);
            return $m;
        }
        return null;
    }
$_SESSION['flash'][$name] = $message;
}

function ensure_logged_in() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php'); exit;
    }
}


function administrador(){
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Acceso denegado");
}
}
?>