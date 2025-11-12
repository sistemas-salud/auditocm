<?php
session_start();
require_once __DIR__ . '/../src/auth.php'; // conexión PDO

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pdo = getPDO();
$busqueda = $_POST['factura'] ?? '';
$factura = null;

try {
    if (!empty($busqueda)) {
        $stmt = $pdo->prepare("
            SELECT * FROM facturas_urg 
            WHERE TRIM(LOWER(factura)) = TRIM(LOWER(?))
            LIMIT 1
        ");
        $stmt->execute([$busqueda]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC); // Trae una sola factura
    }
} catch (PDOException $e) {
    die("Error al consultar las facturas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Facturas URG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fb;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #0078d7;
            margin-bottom: 15px;
            text-align: center;
            width: 100%;
        }

        /* Mensajes de estado */
        .mensaje {
            width: 100%;
            max-width: 700px;
            margin: 10px auto 25px auto;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }

        .mensaje.exito {
            background-color: #e6f4ea;
            color: #1b5e20;
            border: 1px solid #81c784;
        }

        .mensaje.error {
            background-color: #fdecea;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        /* Bloques de factura */
        .factura-contenedor {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            max-height: 600px;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }

        .campo:last-child {
            border-bottom: none;
        }

        .campo label {
            font-weight: bold;
            color: #0078d7;
            margin-bottom: 3px;
        }

        .campo span {
            color: #333;
            word-wrap: break-word;
        }

        /* Botón volver */
        .volver-container {
            width: 100%;
            text-align: center;
            margin-top: 25px;
        }

        .volver {
            display: inline-block;
            text-decoration: none;
            color: #0078d7;
            font-weight: bold;
            padding: 10px 18px;
            border: 1px solid #0078d7;
            border-radius: 5px;
            background: white;
            transition: all 0.2s;
        }

        .volver:hover {
            background-color: #0078d7;
            color: white;
        }

        /* Scroll personalizado */
        .factura-contenedor::-webkit-scrollbar {
            width: 10px;
        }
        .factura-contenedor::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 5px;
        }
        .factura-contenedor::-webkit-scrollbar-thumb:hover {
            background-color: #999;
        }
    </style>
</head>
<body>

<h2>Consulta de Facturas URG</h2>

<?php if (!empty($busqueda)): ?>
    <?php if ($factura): ?>
        <div class="mensaje exito">
            ✅ Se encontró la factura <strong><?= htmlspecialchars($busqueda) ?></strong>.
        </div>

        <div class="factura-contenedor">
            <?php foreach ($factura as $campo => $valor): ?>
                <div class="campo">
                    <label><?= htmlspecialchars($campo) ?>:</label>
                    <span><?= htmlspecialchars($valor ?? '') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mensaje error">
            ⚠️ No se encontró ninguna factura con el número <strong><?= htmlspecialchars($busqueda) ?></strong>.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="volver-container">
    <a class="volver" href="dashboard.php">🔙 Volver al inicio</a>
</div>

</body>
</html>
