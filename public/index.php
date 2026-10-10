<?php
require_once '../config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    /* VALIDAR CONTRASEÑA */
    if (!preg_match('/^\d{6}$/', $password)) {
        $error = 'La contraseña debe contener exactamente 6 números.';
    } else {
        /* CONSULTAR USUARIO Y ROL */
        $stmt = $pdo->prepare(
            "SELECT u.id_usuario, u.usuario, u.nombre, u.password, u.estado, r.id_rol, r.nombre AS rol 
             FROM usuarios u 
             INNER JOIN roles r ON u.id_rol = r.id_rol 
             WHERE u.usuario = :usuario AND u.estado = 1 LIMIT 1"
        );
        $stmt->execute([':usuario' => $usuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        /* VERIFICAR CONTRASEÑA */
        if ($row && password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            
            /* CREAR VARIABLES DE SESIÓN */
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['id_rol'] = $row['id_rol'];
            $_SESSION['rol'] = $row['rol'];
            
            /* REDIRECCIONAR */
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Credenciales incorrectas.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login - Punto de Venta</title>
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="mb-4 text-center">Punto de Venta</h2>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <label class="form-label">Usuario</label>
                            <input class="form-control mb-3" name="usuario" required maxlength="50" autocomplete="username">
                            <label class="form-label">Contraseña numérica</label>
                            <input class="form-control mb-3" name="password" type="password" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" required autocomplete="current-password">
                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>