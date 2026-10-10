<?php
require_once '../config/db.php';

/* VERIFICAR SESIÓN */
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

/* CONSULTAR PRODUCTOS USANDO LA VISTA */
$sql = "SELECT * FROM vista_productos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Productos - Punto de Venta</title>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Productos</h1>
                <p class="text-muted mb-0">Catálogo de productos disponibles</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">Regresar</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($productos) > 0): ?>
                                <?php foreach ($productos as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)$p['id_producto']) ?></td>
                                        <td><?= htmlspecialchars((string)$p['codigo']) ?></td>
                                        <td><?= htmlspecialchars((string)$p['nombre']) ?></td>
                                        <td><?= htmlspecialchars((string)$p['descripcion']) ?></td>
                                        <td>$<?= number_format((float)$p['precio'], 2) ?></td>
                                        <td>
                                            <?php if ($p['stock'] > 0): ?>
                                                <span class="badge bg-success"><?= htmlspecialchars((string)$p['stock']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Sin existencia</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay productos disponibles.</td>
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