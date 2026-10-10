<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// El rol 1 es ADMINISTRADOR. Si entra un VENDEDOR (rol 2), lo bloqueará.
exigirRol([1]); 
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Área de Administración</title>
</head>
<body>
    <h1>Bienvenido al área de Administración</h1>
    <p>Si ves esto, tienes permisos de Administrador.</p>
</body>
</html>