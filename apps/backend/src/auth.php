<?php
function getPDO() {
    $host = 'db';
    $db   = 'auditocm';
    $user = 'auditocm_user';
    $pass = 'auditocm_pass';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($dsn, $user, $pass, $options);
}

function findUserByUsername($username) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function verifyLogin($username, $password) {
    $user = findUserByUsername($username);
    // Aquí se compara contra el campo 'password_hash' de la tabla
    if ($user && $user['password_hash'] === $password) {
        return $user;
    }
    return false;
}
?>
