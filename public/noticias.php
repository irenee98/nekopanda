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

$stmt = $pdo->query('SELECT n.*, ud.nombre, ud.apellidos FROM noticias n JOIN users_data
ud ON n.idUser = ud.idUser ORDER BY n.fecha DESC');
$noticias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias</title>
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
    <div class="noticia">
        <br><h1>Noticias</h1>
        <?php foreach($noticias as $n): ?>
        <article>
        <h2><?=htmlspecialchars($n['titulo'])?></h2>
        <?php if(!empty($n['imagen']) && file_exists(__DIR__.'/imagenes/'. $n['imagen'])): ?>
        <img id="noticia" src="imagenes/<?=htmlspecialchars($n['imagen'])?>" alt="<?=htmlspecialchars($n['titulo'])?>" >
        <?php endif; ?>
        <div><?=nl2br(htmlspecialchars($n['texto']))?></div>
        <p><small>Publicado: <?=htmlspecialchars($n['fecha'])?> por <?=htmlspecialchars($n['nombre'].' '.$n['apellidos'])?></small></p>
        </article><br><br><br>
        <?php endforeach; ?>
    </div>
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