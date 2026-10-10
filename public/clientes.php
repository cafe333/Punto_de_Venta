<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

exigirLogin();

// Capturar el término de búsqueda si existe
$busqueda = trim($_GET['q'] ?? '');
$paramBusqueda = "%" . $busqueda . "%";

// Consulta segura con parámetros únicos
$sql = "SELECT id_cliente, nombre, apellidos, telefono, correo, rfc 
        FROM clientes 
        WHERE estado = 1 
        AND (nombre LIKE :busqueda_nom OR apellidos LIKE :busqueda_ape) 
        ORDER BY apellidos, nombre 
        LIMIT 50";

$stmt = $pdo->prepare($sql);

// Pasamos el mismo valor a los dos parámetros distintos
$stmt->execute([
    ':busqueda_nom' => $paramBusqueda,
    ':busqueda_ape' => $paramBusqueda
]);

$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Clientes - Punto de Venta</title>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Clientes</h1>
            <a href="dashboard.php" class="btn btn-secondary">Regresar</a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get" class="d-flex">
                    <input type="text" name="q" class="form-control me-2" placeholder="Buscar por nombre o apellidos..." value="<?= htmlspecialchars($busqueda) ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="clientes.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>RFC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clientes) > 0): ?>
                                <?php foreach ($clientes as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)$c['id_cliente']) ?></td>
                                        <td><?= htmlspecialchars($c['apellidos'] . ', ' . $c['nombre']) ?></td>
                                        <td><?= htmlspecialchars((string)$c['telefono']) ?></td>
                                        <td><?= htmlspecialchars((string)$c['correo']) ?></td>
                                        <td><?= htmlspecialchars((string)$c['rfc']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No se encontraron clientes.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>