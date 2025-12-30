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
    return "<li><a href= '{$href}', {$active}>{$label}</a></li>";}



    try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=nekopanda;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}

$errors = [];
$success = "";


// Crear noticia
if (isset($_POST['crear'])) {
    $titulo = trim($_POST['titulo']);
    $texto  = trim($_POST['texto']);
    $fecha  = $_POST['fecha'] ?: date('Y-m-d');
    $idUser = $_SESSION['user_id'];

    if ($titulo && $texto && !empty($_FILES['imagen']['name'])) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreImagen = uniqid('img_') . "." . $ext;
        $destino = __DIR__ . "/imagenes/" . $nombreImagen;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, imagen, texto, fecha, idUser) VALUES (?,?,?,?,?)");
            $stmt->execute([$titulo, $nombreImagen, $texto, $fecha, $idUser]);
            $_SESSION['success'] = "Noticia creada correctamente";
            header("Location: noticias-administracion.php");
            exit;
        } else {
            $errors[] = "Error al subir la imagen";
        }
    } else {
        $errors[] = "Título, texto e imagen son obligatorios";
    }
}


// Editar noticia
if (isset($_POST['editar'])) {
    $idNoticia = $_POST['idNoticia'];
    $titulo = trim($_POST['titulo']);
    $texto  = trim($_POST['texto']);
    $fecha  = $_POST['fecha'];

    if ($titulo && $texto) {
        if (!empty($_FILES['imagen']['name'])) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombreImagen = uniqid('img_') . "." . $ext;
            $destino = __DIR__ . "/imagenes/" . $nombreImagen;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, imagen=?, texto=?, fecha=? WHERE idNoticia=?");
                $stmt->execute([$titulo, $nombreImagen, $texto, $fecha, $idNoticia]);
            } else {
                $errors[] = "Error al subir la nueva imagen";
            }
        } else {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, texto=?, fecha=? WHERE idNoticia=?");
            $stmt->execute([$titulo, $texto, $fecha, $idNoticia]);
        }
        if (empty($errors)) {
            $_SESSION['success'] = "Noticia actualizada correctamente";
            header("Location: noticias-administracion.php");
            exit;
        }
    } else {
        $errors[] = "Título y texto son obligatorios";
    }
}


// Borrar noticia
if (isset($_GET['borrar'])) {
    $idNoticia = (int)$_GET['borrar'];
    $pdo->prepare("DELETE FROM noticias WHERE idNoticia=?")->execute([$idNoticia]);
    $_SESSION['success'] = "Noticia eliminada correctamente";
    header("Location: noticias-administracion.php");
    exit;
}



$stmt = $pdo->query("SELECT n.*, u.nombre, u.apellidos
                     FROM noticias n
                     JOIN users_data u ON n.idUser = u.idUser
                     ORDER BY n.fecha DESC");
$noticias = $stmt->fetchAll();

// Boton editar
$editNoticia = null;
if (isset($_GET['editar'])) {
    $idNoticia = (int)$_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE idNoticia=?");
    $stmt->execute([$idNoticia]);
    $editNoticia = $stmt->fetch();
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
  <title>Administrar noticias</title>
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
      <h1>Administración de noticias</h1>

<?php if ($errors): ?>
  <div class="errors">
    <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>


<h2>Crear nueva noticia</h2>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="crear" value="1">
  <label>Título: <br><input type="text" name="titulo" required></label><br><br>
  <label>Texto:<br><textarea name="texto" rows="5" cols="40" required></textarea></label><br><br>
  <label>Imagen: <br><input type="file" name="imagen" accept="image/*" required></label><br><br>
  <label>Fecha: <br><input type="date" name="fecha" value="<?= date('Y-m-d') ?>"></label><br><br><br>
  <button type="submit">Publicar</button>
</form>




<h2>Noticias existentes</h2>
<table>
  <tr>
    <th>Título</th>
    <th>Fecha</th>
    <th>Autor</th>
    <th>Imagen</th>
    <th>Acciones</th>
  </tr>
  <?php foreach ($noticias as $n): ?>
  <tr>
    <td><?= htmlspecialchars($n['titulo']) ?></td>
    <td><?= htmlspecialchars($n['fecha']) ?></td>
    <td><?= htmlspecialchars($n['nombre']." ".$n['apellidos']) ?></td>
    <td>
      <?php if ($n['imagen']): ?>
        <img id="noticiaAdmin" src="imagenes/<?= htmlspecialchars($n['imagen']) ?>" >
      <?php endif; ?>
    </td>
    <td>
      <a href="?editar=<?= $n['idNoticia'] ?>">Editar</a> |
      <a href="?borrar=<?= $n['idNoticia'] ?>" onclick="return confirm('¿Eliminar noticia?')">Borrar</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<?php if ($editNoticia): ?>
<hr>
<h2>Editar noticia</h2>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="editar" value="1">
  <input type="hidden" name="idNoticia" value="<?= $editNoticia['idNoticia'] ?>">
  <label>Título: <br><input type="text" name="titulo" value="<?= htmlspecialchars($editNoticia['titulo']) ?>" required></label><br><br>
  <label>Texto:<br><textarea name="texto" rows="5" cols="40" required><?= htmlspecialchars($editNoticia['texto']) ?></textarea></label><br><br>
  <label>Imagen (subir nueva para reemplazar): <br> <input type="file" name="imagen" accept="image/*"></label><br><br>
  <label>Fecha: <br><input type="date" name="fecha" value="<?= htmlspecialchars($editNoticia['fecha']) ?>"></label><br><br><br>
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
