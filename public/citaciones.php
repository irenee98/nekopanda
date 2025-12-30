<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../configuracion/db.php';
require_once __DIR__ . '/../configuracion/functions.php';
$config = require __DIR__ . '/../configuracion/config.php';
ensure_logged_in();
$idUser = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'guest';
$current = basename($_SERVER['PHP_SELF']);

function navItem($href, $label, $currentpage) {
    $active = $currentpage === $href ? 'class = "active"' : '';
    return "<li><a href= '{$href}', {$active}>{$label}</a></li>";
}


// Crear cita
if(isset($_POST["crear"])){
    $idUser = $_SESSION["user_id"];
    $fecha_cita = $_POST["fecha_cita"];
    $motivo = $_POST["motivo"];
    $hoy = date("Y-m-d");

    if($fecha_cita >= $hoy){
        $stmt = $pdo->prepare("INSERT INTO citas (idUser, fecha_cita, motivo) VALUES (?, ?, ?)");
        $stmt->execute([$idUser, $fecha_cita, $motivo]);
        header("Location: citaciones.php");
        exit;
    }}

// Eliminar cita
if(isset($_GET["eliminar"])){
    $id = $_GET["eliminar"];
    $stmt = $pdo->prepare("DELETE FROM citas WHERE idCita = ?");
    $stmt->execute([$id]);
    $cita = $stmt->fetch();

    if($cita && $cita["fecha_cita"] >= $hoy){
        $stmt = $pdo->prepare("DELETE FROM citas WHERE idCita = ?");
        $stmt->execute([$id]);
        header("Location: citaciones.php");
        exit;
    }}

// Editar cita
$cita = null;
if(isset($_GET["editar"])){
    $id = $_GET["editar"];
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE idCita = ?");
    $stmt->execute([$id]);
    $cita = $stmt->fetch();
}

// Actualizar cita
if(isset($_POST["actualizar"])){
    $id = $_POST["idCita"];
    $fecha_cita = $_POST["fecha_cita"];
    $motivo = $_POST["motivo"];

   if($fecha_cita >= $hoy){
        $stmt = $pdo->prepare("UPDATE citas SET fecha_cita = ?, motivo = ? WHERE idCita = ?");
        $stmt->execute([$fecha_cita, $motivo, $id]);
        header("Location: citaciones.php");
        exit;
    }
}

$stmt = $pdo->query("SELECT * FROM citas ORDER BY fecha_cita ASC");
$citas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citaciones</title>
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
    <section class="citas">
    <h2>Añadir nueva cita</h2>
    <form method="post">
      <label>Fecha de la cita: </label><input type="date" name="fecha_cita" required></label><br><br>
      <label>Motivo: <br></label><textarea name="motivo" placeholder="Motivo de la cita" required></textarea></label><br><br><br>
      <button type="submit" name="crear">Añadir</button>
    </form>

    <?php if($cita): ?>
      <h2>Editar cita</h2>
      <form method="post">
        <input type="hidden" name="idCita" value="<?= $cita['idCita'] ?>">
        <label>Fecha de la cita: <input type="date" name="fecha_cita" value="<?= $cita['fecha_cita'] ?>" required></label><br><br>
        <label>Motivo: <br><textarea name="motivo" required><?= $cita['motivo'] ?></textarea></label><br><br><br>
        <button type="submit" name="actualizar">Guardar cambios</button>
      </form>
    <?php endif; ?>

    <h2>Lista de citas</h2>
    <table>
  <tr>
    <th>Fecha</th>
    <th>Motivo</th>
    <th>Acciones</th>
  </tr>
  <?php foreach ($citas as $row): ?>
  <tr>
    <td><strong>📅 <?= $row['fecha_cita'] ?></strong>
    <td><?= $row['motivo'] ?></td></td>
    <td>
      <a href="citaciones.php?editar=<?= $row['idCita'] ?>">✏️ Editar</a>
      <a href="citaciones.php?eliminar=<?= $row['idCita'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">🗑 Eliminar</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
  </section>
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
