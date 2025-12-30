<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../configuracion/db.php';
require_once __DIR__ . '/../configuracion/functions.php';
$config = require __DIR__ . '/../configuracion/config.php';
$role = $_SESSION['role'] ?? 'guest';
$current = basename($_SERVER['PHP_SELF']);

function navItem($href, $label, $currentpage) {
    $active = $currentpage === $href ? 'class = "active"' : '';
    return "<li><a href= '{$href}', {$active}>{$label}</a></li>";
}


$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST') {
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
if($usuario === '' || $password === '') $errors[] = 'Usuario y contraseña
obligatorios';
if(empty($errors)) {
$stmt = $pdo->prepare('SELECT ul.*, ud.nombre, ud.apellidos FROM
users_login ul JOIN users_data ud ON ul.idUser = ud.idUser WHERE ul.usuario
= ?');
$stmt->execute([$usuario]);
$row = $stmt->fetch();
if (!$row || !password_verify($password, $row['password'])) {
$errors[] = 'Credenciales inválidas';
} else {
$_SESSION['user_id'] = $row['idUser'];
$_SESSION['username'] = $row['usuario'];
$_SESSION['role'] = $row['rol'];
$_SESSION['nombre'] = $row['nombre'];
$_SESSION['apellidos'] = $row['apellidos'];
header('Location: index.php'); exit;
}
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <header>
      <div class="logo">
    <img src="./imagenes/logo.jpg" alt="logo">
  </div><br>
        <nav>
    <ul>
      <?php echo navItem('index.php','index',$current); ?>
      <?php echo navItem('noticias.php','noticias',$current); ?>

      <?php if($role === 'user'): ?>
        <?php echo navItem('citaciones.php','citaciones',$current); ?>
        <?php echo navItem('perfil.php','perfil',$current); ?>
        <?php echo navItem('logout.php','cerrar sesión',$current); ?>
      <?php elseif($role === 'admin'): ?>
        <?php echo navItem('usuarios-administracion.php','usuarios-administracion',$current); ?>
        <?php echo navItem('citas-administracion.php','citaciones-administracion',$current); ?>
        <?php echo navItem('noticias-administracion.php','noticias-administracion',$current); ?>
        <?php echo navItem('perfil.php','perfil',$current); ?>
        <?php echo navItem('logout.php','cerrar sesión',$current); ?>
      <?php else: ?>
        <?php echo navItem('registro.php','registro',$current); ?>
        <?php echo navItem('login.php','login',$current); ?>
      <?php endif; ?>
    </ul>
  </nav>
    </header>
    <main>
        <h1>Login</h1>
        <?php if(!empty($errors)): ?><div class="errors"><?php echo implode('<br>',
        array_map('htmlspecialchars',$errors)); ?></div><?php endif; ?>
        <form method="post">
        <label>Usuario: <input name="usuario"></label><br><br>
        <label>Contraseña: <input name="password" type="password"></label><br><br><br>
        <button>Entrar</button>
        </form>
    </main>
    <footer>
        <div class="social-icons">
      <a href="https://www.facebook.com/" target="_blank"><img src="./imagenes/facebook.png"></a>
      <a href="https://x.com/" target="_blank"><img src="./imagenes/twitter.png"></a>
      <a href="https://www.instagram.com/" target="_blank"><img src="./imagenes/instagram.png"></a>
      <a href="https://www.tiktok.com/" target="_blank"><img src="./imagenes/tiktok.png"></a>
      <a href="mailto:irenerogas@gmail.com" target="_blank"><img src="./imagenes/gmail.png"></a>
    </div>
    </footer>
<script src="./js/script.js"></script>
</body>
</html>
