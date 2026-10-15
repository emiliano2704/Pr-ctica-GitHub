<?php
require 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Obtener datos actuales del producto
$stmt = $pdo->prepare('SELECT * FROM productos_tecnologicos WHERE id = ?');
$stmt->execute([$id]);
$producto = $stmt->fetch();
if (!$producto) {
    die('Producto no encontrado.');
}

// 4. ACTUALIZAR (Modificar producto)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    if ($nombre !== '' && $categoria !== '' && $marca !== '' && $precio !== false && $precio >= 0 && $stock !== false && $stock >= 0) {
        $stmt = $pdo->prepare('UPDATE productos_tecnologicos SET nombre = ?, categoria = ?, marca = ?, precio = ?, stock = ? WHERE id = ?');
        $stmt->execute([$nombre, $categoria, $marca, $precio, $stock, $id]);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar producto | TechStock</title>
    <style>
        :root { color-scheme: dark; --bg: #080b14; --surface: #101625; --line: #29354d; --ink: #f2f6ff; --muted: #a8b5cc; --accent: #6ef2c5; --focus: #9db8ff; }
        * { box-sizing: border-box; } body { min-height: 100vh; margin: 0; padding: 30px 18px; font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif; color: var(--ink); background: radial-gradient(circle at 100% 0, #1d315e 0, transparent 30rem), radial-gradient(circle at 0 100%, #103d40 0, transparent 34rem), var(--bg); }
        main { width: min(700px, 100%); margin: 0 auto; } .back { display: inline-flex; margin-bottom: 25px; color: var(--muted); font-size: .84rem; text-decoration: none; } .back:hover { color: var(--accent); } .back::before { margin-right: 8px; content: "←"; }
        .panel { overflow: hidden; border: 1px solid var(--line); border-radius: 14px; background: linear-gradient(145deg, #141c2e, #101625); } .panel-head { padding: 26px 28px; border-bottom: 1px solid var(--line); } .eyebrow { margin: 0 0 7px; color: var(--accent); font-size: .72rem; font-weight: 800; letter-spacing: .09em; } h1 { margin: 0; font-size: 1.55rem; letter-spacing: -.025em; } .panel-head p:last-child { margin: 7px 0 0; color: var(--muted); font-size: .88rem; }
        form { display: grid; gap: 17px; padding: 28px; } label { display: grid; gap: 7px; color: var(--muted); font-size: .76rem; font-weight: 700; letter-spacing: .015em; } input, select { min-height: 43px; border: 1px solid #3a4862; border-radius: 8px; outline: none; background: #0b101c; color: var(--ink); padding: 9px 11px; font: inherit; transition: border-color 180ms ease, box-shadow 180ms ease; } input:focus, select:focus { border-color: var(--focus); box-shadow: 0 0 0 3px #9db8ff2b; } .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .actions { display: flex; gap: 10px; padding-top: 6px; } button, .cancel { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; border: 1px solid transparent; border-radius: 8px; padding: 9px 15px; font: inherit; font-size: .82rem; font-weight: 750; text-decoration: none; cursor: pointer; transition: transform 180ms ease, background-color 180ms ease; } button { flex: 1; background: var(--accent); color: #052119; } button:hover { background: #98f8d8; transform: translateY(-1px); } .cancel { border-color: #40516e; color: #dce8ff; } .cancel:hover { background: #22314d; } button:focus-visible, .cancel:focus-visible, .back:focus-visible { outline: 3px solid var(--focus); outline-offset: 2px; }
        @media (max-width: 480px) { body { padding: 20px 14px; } .row { grid-template-columns: 1fr; } .panel-head, form { padding-left: 20px; padding-right: 20px; } .actions { flex-direction: column; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { transition-duration: .01ms !important; } }
    </style>
</head>
<body>

<main>
    <a class="back" href="index.php">Volver al inventario</a>
    <section class="panel">
        <div class="panel-head"><p class="eyebrow">ACTUALIZACIÓN DE ACTIVO</p><h1>Editar producto</h1><p>Modifica la información y conserva el inventario siempre sincronizado.</p></div>
        <form method="POST">
            <label>Nombre del producto<input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" maxlength="120" required></label>
            <div class="row">
                <label>Categoría<select name="categoria" required><?php foreach (['Computadoras', 'Smartphones', 'Audio', 'Accesorios', 'Componentes', 'Gaming'] as $categoria): ?><option value="<?= $categoria ?>" <?= $producto['categoria'] === $categoria ? 'selected' : '' ?>><?= $categoria ?></option><?php endforeach; ?></select></label>
                <label>Marca<input type="text" name="marca" value="<?= htmlspecialchars($producto['marca']) ?>" maxlength="80" required></label>
            </div>
            <div class="row">
                <label>Precio (MXN)<input type="number" name="precio" value="<?= htmlspecialchars($producto['precio']) ?>" min="0" step="0.01" required></label>
                <label>Existencias<input type="number" name="stock" value="<?= htmlspecialchars($producto['stock']) ?>" min="0" step="1" required></label>
            </div>
            <div class="actions"><button type="submit" name="actualizar">Guardar cambios</button><a class="cancel" href="index.php">Cancelar</a></div>
        </form>
    </section>
</main>

</body>
</html>
