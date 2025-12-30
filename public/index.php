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
?>


<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Inicio</title>
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
<br><br>
<main>
<div id="main">
<div class="inner">
		<h1>Nuestros productos</h1><br>
		<p class="descripcion">Te dejamos aquí algunos de los ejemplares que podrás encontrar en nuestra tienda, además de productos personalizados hechos a la medida de nuestro cliente.</p>
	<main class="content">
    <div class="card color1">
      <a href="./imagenes/zeronis.jpg">
      <img src="./imagenes/zeronis.jpg" alt="zeronis"></a>
      <h3>Zeronis</h3>
      <p>Peluches importados de Corea del Sur</p>
    </div>
    <div class="card color2">
      <a href="./imagenes/monsterhigh.jpg">
      <img src="./imagenes/monsterhigh.jpg" alt="monsterhigh"></a>
      <h3>Monster High</h3>
      <p>Colección de Funko Pops de Monster High</p>
    </div>
    <div class="card color3">
      <a href="./imagenes/hironos.jpg">
      <img src="./imagenes/hironos.jpg" alt="hironos"></a>
      <h3>Hironos</h3>
      <p>Una de las líneas más famosas de Pop Mart</p>
    </div>
    <div class="card color4">
      <a href="./imagenes/bt21.jpg">
      <img src="./imagenes/bt21.jpg" alt="bt21"></a>
      <h3>BT21</h3>
      <p>Cojines en colaboración con Miniso</p>
    </div>
    <div class="card color5">
      <a href="./imagenes/lightring.jpg">
      <img src="./imagenes/lightring.jpg" alt="lightring"></a>
      <h3>Lightrings</h3>
      <p>Llaveros que iluminan</p>
    </div>
    <div class="card color6">
      <a href="./imagenes/nails.jpg">
      <img src="./imagenes/nails.jpg" alt="nails"></a>
      <h3>Uñas postizas</h3>
      <p>Uñas personalizadas con la temática que quieras</p>
    </div>
    <div class="card color7">
      <a href="./imagenes/phonecase.jpg">
      <img src="./imagenes/phonecase.jpg" alt="phonecase"></a>
      <h3>Fundas</h3>
      <p>Fundas de Stitch para el móvil</p>
    </div>
    <div class="card color8">
      <a href="./imagenes/skzoo.jpg">
      <img src="./imagenes/skzoo.jpg" alt="skzoo"></a>
      <h3>SKZOO</h3>
      <p>Camisetas de tus personajes favoritos</p>
    </div>
    <div class="card color9">
      <a href="./imagenes/sanrio.jpg">
      <img src="./imagenes/sanrio.jpg" alt="Sanrio"></a>
      <h3>Sanrio</h3>
      <p>Funko Pops de Sanrio (special edition)</p>
    </div>
  </main>
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
