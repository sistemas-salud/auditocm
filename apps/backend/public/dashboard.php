<?php
session_start();

// Validar que el usuario esté autenticado
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Facturas - AuditoCM</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fb;
            margin: 0;
            padding: 40px;
            text-align: center;
        }
        .dashboard-container {
            background-color: white;
            border-radius: 10px;
            max-width: 500px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #0078d7;
        }
        input[type="text"] {
            padding: 8px;
            width: 80%;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        button {
            padding: 8px 20px;
            background-color: #0078d7;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #005fa3;
        }
        form {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Bienvenido, <?= htmlspecialchars($user) ?> 👋</h2>
        <p>Ingrese el número de factura que desea consultar:</p>

        <!-- 🔹 Aquí corregimos el name para que coincida -->
        <form method="post" action="consultar_factura.php">
            <input type="text" name="factura" placeholder="Número de factura" required>
            <button type="submit">Consultar</button>
        </form>

        <form method="post" action="logout.php">
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>
</body>
</html>
