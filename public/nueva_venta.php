<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

exigirLogin();

$mensaje = '';
$error = '';

// Obtener listas para los selectores del formulario
$clientes = $pdo->query("SELECT id_cliente, nombre, apellidos FROM clientes WHERE estado = 1")->fetchAll();
$productos = $pdo->query("SELECT id_producto, codigo, nombre, precio, stock FROM productos WHERE estado = 1 AND stock > 0")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = filter_var($_POST['id_cliente'], FILTER_VALIDATE_INT);
    $id_producto = filter_var($_POST['id_producto'], FILTER_VALIDATE_INT);
    $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);

    if (!$id_cliente || !$id_producto || !$cantidad || $cantidad <= 0) {
        $error = "Datos inválidos. Verifica que la cantidad sea mayor a 0.";
    } else {
        try {
            // 1. Iniciar la transacción
            $pdo->beginTransaction();

            // 2. Verificar stock y obtener precio (FOR UPDATE bloquea la fila para evitar que otro venda al mismo tiempo)
            $stmtProd = $pdo->prepare("SELECT precio, stock FROM productos WHERE id_producto = :id FOR UPDATE");
            $stmtProd->execute([':id' => $id_producto]);
            $prod = $stmtProd->fetch();

            if (!$prod || $prod['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente para realizar la venta.");
            }

            $precio = $prod['precio'];
            $subtotal = $precio * $cantidad;
            $iva = $subtotal * 0.16; // Suponiendo 16% de IVA
            $total = $subtotal + $iva;

            // 3. Insertar el encabezado de la Venta
            $stmtVenta = $pdo->prepare("INSERT INTO ventas (id_cliente, id_usuario, subtotal, iva, total, estado) VALUES (:cliente, :usuario, :subtotal, :iva, :total, 'PAGADA')");
            $stmtVenta->execute([
                ':cliente' => $id_cliente,
                ':usuario' => $_SESSION['id_usuario'],
                ':subtotal' => $subtotal,
                ':iva' => $iva,
                ':total' => $total
            ]);
            $idVenta = $pdo->lastInsertId();

            // 4. Insertar el Detalle de la venta
            $stmtDetalle = $pdo->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio, subtotal) VALUES (:venta, :producto, :cantidad, :precio, :subtotal)");
            $stmtDetalle->execute([
                ':venta' => $idVenta,
                ':producto' => $id_producto,
                ':cantidad' => $cantidad,
                ':precio' => $precio,
                ':subtotal' => $subtotal
            ]);

            // 5. Actualizar el Stock del producto
            $stmtUpdateStock = $pdo->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id");
            $stmtUpdateStock->execute([
                ':cantidad' => $cantidad,
                ':id' => $id_producto
            ]);

            // 6. Confirmar transacción
            $pdo->commit();
            $mensaje = "Venta registrada exitosamente (Folio: $idVenta).";

        } catch (Exception $e) {
            // Si cualquier paso falla, deshacer todo (Rollback)
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Nueva Venta</title>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Registrar Venta</h2>
            <a href="dashboard.php" class="btn btn-secondary">Regresar</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select class="form-select" name="id_cliente" required>
                            <option value="">Selecciona un cliente...</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>"><?= htmlspecialchars($c['apellidos'] . ' ' . $c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select class="form-select" name="id_producto" required>
                            <option value="">Selecciona un producto...</option>
                            <?php foreach ($productos as $p): ?>
                                <option value="<?= $p['id_producto'] ?>">
                                    <?= htmlspecialchars($p['codigo'] . ' - ' . $p['nombre']) ?> 
                                    (Disp: <?= $p['stock'] ?> | $<?= number_format((float)$p['precio'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" class="form-control" name="cantidad" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Procesar Venta</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>