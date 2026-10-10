<?php
declare(strict_types=1);
session_start();

$host = '127.0.0.1';
$db   = 'punto_venta';
$user = 'pv_app';
$pass = 'Cambiar_Esta_Clave_Larga_2026!';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user, 
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    exit('Error de conexión a la base de datos.');
}
?>