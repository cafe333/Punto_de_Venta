<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function exigirLogin(): void {
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ../public/index.php');
        exit;
    }
}

function exigirRol(array $rolesPermitidos): void {
    exigirLogin();
    if (!in_array((int)$_SESSION['id_rol'], $rolesPermitidos, true)) {
        http_response_code(403);
        exit('Acceso denegado. No tienes permisos para ver esta página.');
    }
}
?>