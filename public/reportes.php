<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

exigirLogin();

$productosBajoStock = [];
$ventasPeriodo = [];
$inicio = $_GET['inicio'] ?? date('Y-m-01');
$fin = $_GET['fin'] ?? date('Y-m-t');
$error = '';

try {
    // 1. Llamar procedimiento almacenado de bajo stock
    $stmt1 = $pdo->prepare("CALL sp_productos_bajo_stock()");
    $stmt1->execute();
    $productosBajoStock = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    
    // Importante en PDO para liberar el cursor tras un CALL de MySQL
    $stmt1->closeCursor();

    // 2. Llamar procedimiento almacenado de ventas por periodo
    $stmt2 = $pdo->prepare("CALL sp_ventas_por_periodo(:inicio, :fin)");
    $stmt2->execute([':inicio' => $inicio, ':fin' => $fin]);
    $ventasPeriodo = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $stmt2->closeCursor();

} catch (PDOException $e) {
    $error = "Error al cargar los reportes: " . $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Reportes - Punto de Venta</title>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Reportes del Sistema</h2>
            <a href="dashboard.php" class="btn btn-secondary">Regresar al Dashboard</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Sección de Ventas por Periodo -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">Ventas por Periodo (Procedimiento Almacenado)</div>
            <div class="card-body">
                <form method="get" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="inicio" class="form-control" value="<?= htmlspecialchars($inicio) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fin" class="form-control" value="<?= htmlspecialchars($fin) ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrar Ventas</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Venta</th>
                                <th>ID Cliente</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($ventasPeriodo) > 0): ?>
                                <?php foreach ($ventasPeriodo as $v): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)$v['id_venta']) ?></td>
                                        <td><?= htmlspecialchars((string)$v['id_cliente']) ?></td>
                                        <td>$<?= number_format((float)$v['subtotal'], 2) ?></td>
                                        <td>$<?= number_format((float)$v['iva'], 2) ?></td>
                                        <td>$<?= number_format((float)$v['total'], 2) ?></td>
                                        <td><?= htmlspecialchars((string)$v['estado']) ?></td>
                                        <td><?= htmlspecialchars((string)$v['fecha']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay ventas registradas en este periodo.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sección de Productos con Bajo Stock -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">Productos con Bajo Stock (Procedimiento Almacenado)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($productosBajoStock) > 0): ?>
                                <?php foreach ($productosBajoStock as $ps): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)$ps['codigo']) ?></td>
                                        <td><?= htmlspecialchars((string)$ps['nombre']) ?></td>
                                        <td><span class="badge bg-danger"><?= htmlspecialchars((string)$ps['stock']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay productos con bajo stock.</td>
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