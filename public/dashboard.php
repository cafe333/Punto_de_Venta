<?php
require_once '../config/db.php';

if (!isset($_SESSION['id_usuario'])) { 
    header('Location: index.php'); 
    exit; 
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body>
    <div class="container py-4">
        <h1>Dashboard</h1>
        <p>Usuario: <?=htmlspecialchars($_SESSION['usuario'])?> | Rol: <?=htmlspecialchars($_SESSION['rol'])?></p>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="productos.php">Productos</a>
            <a class="list-group-item list-group-item-action" href="clientes.php">Clientes</a>
            <a class="list-group-item list-group-item-action" href="nueva_venta.php">Registrar Venta</a>
            <a class="list-group-item list-group-item-action" href="logout.php">Cerrar sesión</a>
            <a class="list-group-item list-group-item-action" href="reportes.php">Reportes y Procedimientos</a>
        </div>
    </div>
</body>
</html>