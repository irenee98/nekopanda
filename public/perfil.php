<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../configuracion/db.php';
require_once __DIR__ . '/../configuracion/functions.php';
$config = require __DIR__ . '/../configuracion/config.php';
ensure_logged_in();
$idUser = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'guest';
$current = basename($_SERVER['PHP_SELF']);

function navItem($href, $label, $currentPage) {
    $active = $currentPage === $href ? 'class="active"' : '';
    return "<li><a href='{$href}' {$active}>{$label}</a></li>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE users_data 
                           SET nombre=?, apellidos=?, telefono=?, direccion=?, sexo=?, email=? 
                           WHERE idUser=?");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['apellidos'],
        $_POST['telefono'],
        $_POST['direccion'],
        $_POST['sexo'],
        $_POST['email'],
        $idUser
    ]);

    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt2 = $pdo->prepare('UPDATE users_login SET password = ? WHERE idUser = ?');
        $stmt2->execute([$hash, $idUser]);
    }

    header('Location: perfil.php');
    exit;
}
$stmt = $pdo->prepare('SELECT ud.*, ul.usuario FROM users_data ud JOIN
users_login ul ON ud.idUser = ul.idUser WHERE ud.idUser=?');
$stmt->execute([$idUser]);
$user = $stmt->fetch();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
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
    <h1>Perfil</h1>
<form method="post">
 <label>Usuario (no modificable): <br><input type="text" value="<?=htmlspecialchars($user['usuario'])?>" disabled></label><br><br>
 <label>Nombre: <br><input type="text" name="nombre" value="<?=htmlspecialchars($user['nombre'])?>"></label><br><br>
 <label>Apellidos: <br><input  type="text" name="apellidos" value="<?=htmlspecialchars($user['apellidos'])?>"></label><br><br>
 <label>Email: <br><input type="email" name="email" value="<?=htmlspecialchars($user['email'])?>"></label><br><br>
 <label>Teléfono: <br><input  type="text" name="telefono" value="<?=htmlspecialchars($user['telefono'])?>"></label><br><br>
 <label>Dirección: <textarea name="direccion"><?=htmlspecialchars($user['direccion'])?></textarea></label><br><br>
 <label>Sexo: <br>
      <select name="sexo">
        <option value="Hombre" <?= $user['sexo']==='Hombre'?'selected':'' ?>>Hombre</option>
        <option value="Mujer" <?= $user['sexo']==='Mujer'?'selected':'' ?>>Mujer</option>
        <option value="Sin especificar" <?= $user['sexo']==='Sin especificar'?'selected':'' ?>>Sin especificar</option>
      </select>
  </label><br><br>
 <label>Nueva contraseña (dejar vacío para no cambiar): <input name="password" type="password"></label><br><br><br>
 <button>Guardar</button>
</form>
</main><br><br>
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
