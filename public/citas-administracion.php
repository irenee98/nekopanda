<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../configuracion/db.php';
require_once __DIR__ . '/../configuracion/functions.php';
$config = require __DIR__ . '/../configuracion/config.php';
administrador();
$role = $_SESSION['role'] ?? 'guest';
$current = basename($_SERVER['PHP_SELF']);

function navItem($href, $label, $currentpage) {
    $active = $currentpage === $href ? 'class = "active"' : '';
    return "<li><a href= '{$href}', {$active}>{$label}</a></li>";
}

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=nekopanda;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}

$errors = [];
$success = "";

// Crear cita
if (isset($_POST['crear'])) {
    $idUser = $_POST['idUser'];
    $fecha  = $_POST['fecha_cita'];
    $motivo = $_POST['motivo'];

    if ($idUser && $fecha) {
        $stmt = $pdo->prepare("INSERT INTO citas (idUser, fecha_cita, motivo) VALUES (?,?,?)");
        $stmt->execute([$idUser, $fecha, $motivo]);
        $_SESSION['success'] = "Cita creada correctamente";
        header("Location: citas-administracion.php");
        exit;
    } else {
        $errors[] = "Usuario y fecha son obligatorios";
    }
}

// Editar cita
if (isset($_POST['editar'])) {
    $idCita = $_POST['idCita'];
    $idUser = $_POST['idUser'];
    $fecha  = $_POST['fecha_cita'];
    $motivo = $_POST['motivo'];

    if ($idCita && $idUser && $fecha) {
        $stmt = $pdo->prepare("UPDATE citas SET idUser=?, fecha_cita=?, motivo=? WHERE idCita=?");
        $stmt->execute([$idUser, $fecha, $motivo, $idCita]);
        $_SESSION['success'] = "Cita actualizada correctamente";
        header("Location: citas-administracion.php");
        exit;
    } else {
        $errors[] = "Todos los campos son obligatorios";
    }
}


// Borrar cita
if (isset($_GET['borrar'])) {
    $idCita = (int)$_GET['borrar'];
    $pdo->prepare("DELETE FROM citas WHERE idCita=?")->execute([$idCita]);
    $_SESSION['success'] = "Cita eliminada correctamente";
    header("Location: citas-administracion.php");
    exit;
}



$stmt = $pdo->query("SELECT c.idCita, c.fecha_cita, c.motivo, u.idUser, u.nombre, u.apellidos
                     FROM citas c
                     JOIN users_data u ON c.idUser = u.idUser
                     ORDER BY c.fecha_cita ASC");
$citas = $stmt->fetchAll();


$usuarios = $pdo->query("
  SELECT ud.idUser, ud.nombre, ud.apellidos
  FROM users_data ud
  JOIN users_login ul ON ud.idUser = ul.idUser
  WHERE ul.rol = 'user'
  ORDER BY ud.nombre
")->fetchAll();


$editCita = null;
if (isset($_GET['editar'])) {
    $idCita = (int)$_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE idCita=?");
    $stmt->execute([$idCita]);
    $editCita = $stmt->fetch();
}



if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>


<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Administrar citas</title>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<header>
  <div class="logo">
    <img src="./imagenes/logo.jpg" alt="logo">
  </div><br></ul>
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
        <?php echo navItem('citas-administracion.php','citas-administracion',$current); ?>
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
    <h1>Administración de citas</h1>

<?php if ($errors): ?>
  <div class="errors">
    <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>


<h2>Crear nueva cita</h2>
<form method="post">
  <input type="hidden" name="crear" value="1">
  <label>Usuario:<br>
    <select name="idUser" required>
      <option value="">-- Selecciona usuario --</option>
      <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['idUser'] ?>">
          <?= htmlspecialchars($u['nombre']." ".$u['apellidos']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label><br><br>
  <label>Fecha: <br><input type="date" name="fecha_cita" required></label><br><br>
  <label>Motivo: <br><textarea name="motivo"></textarea></label><br><br><br>
  <button type="submit">Crear</button>
</form>

<hr>


<h2>Lista de citas</h2>
<table>
  <tr>
    <th>Usuario</th>
    <th>Fecha</th>
    <th>Motivo</th>
    <th>Acciones</th>
  </tr>
  <?php foreach ($citas as $c): ?>
  <tr>
    <td><?= htmlspecialchars($c['nombre']." ".$c['apellidos']) ?></td>
    <td><?= htmlspecialchars($c['fecha_cita']) ?></td>
    <td><?= htmlspecialchars($c['motivo']) ?></td>
    <td>
      <a href="?editar=<?= $c['idCita'] ?>">Editar</a> |
      <a href="?borrar=<?= $c['idCita'] ?>" onclick="return confirm('¿Eliminar cita?')">Borrar</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<?php if ($editCita): ?>
<hr>
<h2>Editar cita #<?= $editCita['idCita'] ?></h2>
<form method="post">
  <input type="hidden" name="editar" value="1">
  <input type="hidden" name="idCita" value="<?= $editCita['idCita'] ?>">
  <label>Usuario:<br>
    <select name="idUser" required>
      <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['idUser'] ?>" <?= $editCita['idUser']==$u['idUser']?'selected':'' ?>>
          <?= htmlspecialchars($u['nombre']." ".$u['apellidos']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label><br><br>
  <label>Fecha: <br><input type="date" name="fecha_cita" value="<?= htmlspecialchars($editCita['fecha_cita']) ?>" required></label><br><br>
  <label>Motivo: <br><textarea name="motivo"><?= htmlspecialchars($editCita['motivo']) ?></textarea></label><br><br><br>
  <button type="submit">Actualizar</button>
</form>
<?php endif; ?>
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
