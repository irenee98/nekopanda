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


// Crear usuario
if (isset($_POST['crear'])) {
    $nombre     = trim($_POST['nombre']);
    $apellidos  = trim($_POST['apellidos']);
    $email      = trim($_POST['email']);
    $telefono   = trim($_POST['telefono']);
    $fecha      = $_POST['fecha_nac'];
    $direccion  = $_POST['direccion'];
    $sexo       = $_POST['sexo'] ?? 'Sin especificar';
    $usuario    = trim($_POST['usuario']);
    $contrasena = $_POST['password'];
    $rol        = $_POST['rol'];

    if ($nombre && $apellidos && $email && $usuario && $contrasena) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nac, direccion, sexo) 
                                   VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo]);
            $idUser = $pdo->lastInsertId();

            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt2 = $pdo->prepare("INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?,?,?,?)");
            $stmt2->execute([$idUser, $usuario, $hash, $rol]);

            $pdo->commit();
            $success = "Usuario creado correctamente";
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Error al crear usuario: " . $e->getMessage();
        }
    } else {
        $errors[] = "Faltan campos obligatorios";
    }
}


// Editar usuario
if (isset($_POST['editar'])) {
    $idUser     = $_POST['idUser'];
    $nombre     = trim($_POST['nombre']);
    $apellidos  = trim($_POST['apellidos']);
    $email      = trim($_POST['email']);
    $telefono   = trim($_POST['telefono']);
    $fecha      = $_POST['fecha_nac'];
    $direccion  = $_POST['direccion'];
    $sexo       = $_POST['sexo'] ?? 'Sin especificar';
    $usuario    = trim($_POST['usuario']);
    $rol        = $_POST['rol'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE users_data 
                               SET nombre=?, apellidos=?, email=?, telefono=?, fecha_nac=?, direccion=?, sexo=? 
                               WHERE idUser=?");
        $stmt->execute([$nombre, $apellidos, $email, $telefono, $fecha, $direccion, $sexo, $idUser]);

        if (!empty($_POST['contrasena'])) {
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt2 = $pdo->prepare("UPDATE users_login 
                                    SET usuario=?, password=?, rol=? 
                                    WHERE idUser=?");
            $stmt2->execute([$usuario, $hash, $rol, $idUser]);
        } else {
            $stmt2 = $pdo->prepare("UPDATE users_login 
                                    SET usuario=?, rol=? 
                                    WHERE idUser=?");
            $stmt2->execute([$usuario, $rol, $idUser]);
        }

        $pdo->commit();
        $_SESSION['success'] = "Usuario actualizado correctamente";
        header("Location: usuarios-administracion.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $errors[] = "Error al editar usuario: " . $e->getMessage();
    }
}


// Borrar usuario
if (isset($_GET['borrar'])) {
    $idUser = (int)$_GET['borrar'];
    $pdo->prepare("DELETE FROM users_data WHERE idUser=?")->execute([$idUser]);
    header("Location: usuarios-administracion.php");
    exit;
}


$stmt = $pdo->query("SELECT ud.idUser, ud.nombre, ud.apellidos, ud.email, ud.telefono, 
                            ud.fecha_nac, ud.direccion, ud.sexo,
                            ul.usuario, ul.rol
                     FROM users_data ud 
                     JOIN users_login ul ON ud.idUser = ul.idUser 
                     ORDER BY ud.idUser ASC");
$usuarios = $stmt->fetchAll();

$editUser = null;
if (isset($_GET['editar'])) {
    $idUser = (int)$_GET['editar'];
    $stmt = $pdo->prepare("SELECT ud.*, ul.usuario, ul.rol 
                           FROM users_data ud 
                           JOIN users_login ul ON ud.idUser = ul.idUser 
                           WHERE ud.idUser=?");
    $stmt->execute([$idUser]);
    $editUser = $stmt->fetch();
}
?>


<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Administrar usuarios</title>
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
    <h1>Administrar usuarios</h1>

<?php if ($errors): ?>
  <div class="errors">
    <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>


<h2>Crear usuario nuevo</h2>
<form method="post">
  <input type="hidden" name="crear" value="1">
  <label>Nombre: <input type="text" name="nombre" required></label><br><br>
  <label>Apellidos: <input type="text" name="apellidos" required></label><br><br>
  <label>Email: <br><input type="email" name="email" required></label><br><br>
  <label>Teléfono: <input type="text" name="telefono"></label><br><br>
  <label>Fecha nacimiento: <input type="date" name="fecha_nac"></label><br><br>
  <label>Dirección: <input type="text" name="direccion"></label><br><br>
  <label>Sexo: <br>
    <select name="sexo">
      <option value="Hombre">Hombre</option>
      <option value="Mujer">Mujer</option>
      <option value="Sin especificar" selected>Sin especificar</option>
    </select>
  </label><br><br><br>
  <label>Usuario: <br><input type="text" name="usuario" required></label><br><br>
  <label>Contraseña: <br><input type="password" name="password" required></label><br><br>
  <label>Rol:<br>
    <select name="rol">
      <option value="user">Usuario</option>
      <option value="admin">Administrador</option>
    </select>
  </label><br><br><br>
  <button type="submit">Crear</button>
</form>


<h2>Lista de usuarios</h2>
<table>
  <tr>
    <th>Usuario</th>
    <th>Nombre</th>
    <th>Email</th>
    <th>Rol</th>
    <th>Acciones</th>
  </tr>
  <?php foreach ($usuarios as $u): ?>
  <tr>
    <td><?= htmlspecialchars($u['usuario']) ?></td>
    <td><?= htmlspecialchars($u['nombre'] . " " . $u['apellidos']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= htmlspecialchars($u['rol']) ?></td>
    <td>
      <a href="?editar=<?= $u['idUser'] ?>">Editar</a> |
      <a href="?borrar=<?= $u['idUser'] ?>" onclick="return confirm('¿Eliminar usuario?')">Borrar</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<?php if ($editUser): ?>
<hr>
<h2>Editar usuario <?= htmlspecialchars($editUser['usuario']) ?></h2>
<form method="post">
  <input type="hidden" name="editar" value="1">
  <input type="hidden" name="idUser" value="<?= $editUser['idUser'] ?>">
  <label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($editUser['nombre']) ?>" required></label><br><br>
  <label>Apellidos: <input type="text" name="apellidos" value="<?= htmlspecialchars($editUser['apellidos']) ?>" required></label><br><br>
  <label>Email: <br><input type="email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>" required></label><br><br>
  <label>Teléfono: <input type="text" name="telefono" value="<?= htmlspecialchars($editUser['telefono']) ?>"></label><br><br>
  <label>Fecha nacimiento: <input type="date" name="fecha_nac" value="<?= htmlspecialchars($editUser['fecha_nac']) ?>"></label><br><br>
  <label>Dirección: <input type="text" name="direccion" value="<?= htmlspecialchars($editUser['direccion']) ?>"></label><br><br>
  <label>Sexo:<br>
    <select name="sexo">
      <option value="Hombre" <?= $editUser['sexo']==='Hombre'?'selected':'' ?>>Hombre</option>
      <option value="Mujer" <?= $editUser['sexo']==='Mujer'?'selected':'' ?>>Mujer</option>
      <option value="Sin especificar" <?= $editUser['sexo']==='Sin especificar'?'selected':'' ?>>Sin especificar</option>
    </select>
  </label><br><br>
  <label>Usuario: <br><input type="text" name="usuario" value="<?= htmlspecialchars($editUser['usuario']) ?>" required></label><br><br>
  <label>Nueva contraseña (dejar vacío si no cambia): <br><input type="password" name="password"></label><br><br>
  <label>Rol: <br>
    <select name="rol">
      <option value="user" <?= $editUser['rol']==='user'?'selected':'' ?>>Usuario</option>
      <option value="admin" <?= $editUser['rol']==='admin'?'selected':'' ?>>Administrador</option>
    </select>
  </label><br><br><br>
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
