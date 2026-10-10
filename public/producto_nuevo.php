<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// Proteger la ruta: Solo Administradores pueden agregar productos
exigirRol([1]);

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);

    /* VALIDACIONES */
    if (!$codigo || !$nombre || $precio === false || $stock === false) {
        $error = 'Por favor, completa todos los campos requeridos correctamente.';
    } elseif ($precio < 0 || $stock < 0) {
        $error = 'El precio y el stock no pueden ser valores negativos.';
    } else {
        /* INSERCIÓN CON CONSULTA PREPARADA */
        try {
            $sql = "INSERT INTO productos (codigo, nombre, descripcion, precio, stock, estado) 
                    VALUES (:codigo, :nombre, :descripcion, :precio, :stock, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':codigo' => $codigo,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':stock' => $stock
            ]);
            $mensaje = 'Producto agregado exitosamente.';
        } catch (PDOException $e) {
            $error = 'Error al guardar el producto. Verifica que el código no esté duplicado.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Nuevo Producto</title>
</head>
<body class="bg-light">
    <div class="container py-4">
        <h2>Agregar Nuevo Producto</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Código del Producto</label>
                        <input type="text" class="form-control" name="codigo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" class="form-control" name="descripcion">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio ($)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="precio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Inicial</label>
                            <input type="number" min="0" class="form-control" name="stock" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar Producto</button>
                    <a href="productos.php" class="btn btn-secondary">Regresar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>