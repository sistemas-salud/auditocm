<?php
require_once __DIR__ . '/../src/auth.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = verifyLogin($username, $password);

    if ($user) {
        $_SESSION['user'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - AuditoCM</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h3>Aplicación de Bases de datos de Auditoría de cuentas médicas</h3>
        <div class="login-divider">
            <hr>
            <h3>Inicio de sesión</h3>
            <hr>
        </div>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <!-- <label for="username">Usuario:</label> -->
            <input type="text" name="username" id="username" required placeholder = "Usuario*" >
            

            <!-- <label for="password">Contraseña:</label> -->
            <input type="password" name="password" id="password" required placeholder = "Contraseña*" >

            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
