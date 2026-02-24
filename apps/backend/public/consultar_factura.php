<?php
session_start();
require_once __DIR__ . '/../src/auth.php'; // conexión PDO

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pdo = getPDO();

// ❌ ERROR CORREGIDO: DEBE USARSE $_GET YA QUE EL DATO VIENE POR LA URL
// La factura viene de la URL (ej: consultar_factura.php?factura=12345)
$busqueda = $_GET['factura'] ?? ''; 
$factura = null;

$busqueda2 = $_GET['anio'] ?? ''; 
$anio = null;

try {
    if (!empty($busqueda)) {
        $stmt = $pdo->prepare("
            SELECT * FROM facturas_mig 
            WHERE TRIM(LOWER(factura)) = TRIM(LOWER(?))
            LIMIT 1
        ");
        $stmt->execute([$busqueda]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC); // Trae una sola factura
    }
} catch (PDOException $e) {
    // Es mejor mostrar este error de forma más amigable al usuario en producción
    die("Error al consultar las facturas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Facturas Mig</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="consulta">

<h2 class="consulta">Consulta de Facturas MIG</h2>

<?php 
// Si $busqueda está vacío (ej. alguien entró a la página sin un número), 
// mostramos un mensaje para la mejor experiencia de usuario.
if (empty($busqueda)): ?>
    <div class="mensaje error">
        ⚠️ No se ha especificado ningún número de factura para la consulta.
    </div>
<?php elseif ($factura): ?>
    <div class="mensaje exito">
        ✅ Se encontró la factura <strong><?= htmlspecialchars($busqueda) ?></strong>.
    </div>

    <div class="factura-contenedor">
        <?php foreach ($factura as $campo => $valor): ?>
            <div class="campo">
                <label><?= htmlspecialchars(ucwords(str_replace('_', ' ', $campo))) ?>:</label>
                <span><?= htmlspecialchars($valor ?? 'N/A') ?></span>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="mensaje error">
        ⚠️ No se encontró ninguna factura con el número <strong><?= htmlspecialchars($busqueda) ?></strong>.
    </div>
<?php endif; ?>

<div class="volver-container">
    <a class="volver" href="dashboard.php">Volver al inicio</a>
</div>

</body>
</html>
