<?php
require_once "../config/db.php";

$usuario = "vendedor";
$password = "123456";
$nombre = "Juan Vendedor";
$id_rol = 2; // 2 es el id para VENDEDOR en la base de datos

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (usuario, password, nombre, id_rol, estado) 
        VALUES (:usuario, :password, :nombre, :id_rol, 1)";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':usuario' => $usuario,
        ':password' => $password_hash,
        ':nombre' => $nombre,
        ':id_rol' => $id_rol
    ]);
    echo "Usuario '$usuario' creado correctamente. <br>";
    echo "<a href='index.php'>Ir al Login</a>";
} catch (PDOException $e) {
    echo "Error al crear usuario: " . $e->getMessage();
}
?>