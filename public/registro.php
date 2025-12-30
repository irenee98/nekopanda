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
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$fecha_nac = $_POST['fecha_nac'] ?? '';
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';


if($nombre === '') $errors[] = 'Nombre es obligatorio';
if($apellidos === '') $errors[] = 'Apellidos es obligatorio';
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
if($telefono === '') $errors[] = 'Teléfono obligatorio';
if($fecha_nac === '') $errors[] = 'Fecha de nacimiento obligatoria';
if($usuario === '') $errors[] = 'Usuario obligatorio';
if(strlen($password) < 6) $errors[] = 'Contraseña mínima 6 caracteres';



$stmt = $pdo->prepare('SELECT 1 FROM users_login WHERE usuario = ? OR idUser IN (SELECT idUser FROM users_data WHERE email = ?)');
$stmt->execute([$usuario, $email]);
if($stmt->fetch()) $errors[] = 'Usuario o email ya existente';


if(empty($errors)) {
try {
$pdo->beginTransaction();
$stmt = $pdo->prepare('INSERT INTO users_data (nombre,apellidos,email,telefono,fecha_nac,direccion,sexo) VALUES (?,?,?,?,?,?,?)');
$stmt->execute([$nombre,$apellidos,$email,$telefono,$fecha_nac,$_POST['direccion'] ?? '', $_POST['sexo'] ?? 'O']);
$idUser = $pdo->lastInsertId();
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt2 = $pdo->prepare('INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?,?,?,?)');
$stmt2->execute([$idUser, $usuario, $hash, 'user']);
$pdo->commit();
$_SESSION['message'] = 'Registro correcto. Puedes iniciar sesión.';
header('Location: login.php'); exit;
} catch (Exception $e) {
$pdo->rollBack();
$errors[] = 'Error en el registro: ' . $e->getMessage();
}}}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
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
        <h1>Registro</h1>
        <?php if(!empty($errors)): ?>
        <ul class="errors"><?php foreach($errors as $err) echo "<li>".htmlspecialchars($err)."</li>"; ?></ul>
        <?php endif; ?>
        <form method="post" novalidate>
        <label>Nombre: <input name="nombre"></label><br><br>
        <label>Apellidos: <input name="apellidos"></label><br><br>
        <label>Email: <input name="email" type="email"></label><br><br>
        <label>Teléfono: <input name="telefono"></label><br><br>
        <label>Fecha de nacimiento: <input name="fecha_nac" type="date"></label><br><br>
        <label>Usuario: <input name="usuario"></label><br><br>
        <label>Contraseña: <input name="password" type="password"></label><br><br><br>
        <button>Registrar</button>
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
