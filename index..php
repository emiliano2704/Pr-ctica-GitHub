<?php
require 'conexion.php';

// 1. CREAR (Insertar producto tecnológico)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    if ($nombre !== '' && $categoria !== '' && $marca !== '' && $precio !== false && $precio >= 0 && $stock !== false && $stock >= 0) {
        $stmt = $pdo->prepare('INSERT INTO productos_tecnologicos (nombre, categoria, marca, precio, stock) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nombre, $categoria, $marca, $precio, $stock]);
        header('Location: index.php');
        exit;
    }
}

// 2. BORRAR (Eliminar producto)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM productos_tecnologicos WHERE id = ?');
        $stmt->execute([$id]);
    }
    header('Location: index.php');
    exit;
}

// 3. LEER (Obtener todos los productos)
$stmt = $pdo->query('SELECT * FROM productos_tecnologicos ORDER BY id DESC');
$productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechStock | Inventario tecnológico</title>
    <style>
        :root { color-scheme: dark; --bg: #080b14; --surface: #101625; --surface-raised: #171f31; --line: #29354d; --ink: #f2f6ff; --muted: #a8b5cc; --accent: #6ef2c5; --accent-deep: #0e8f73; --danger: #ff758d; --focus: #9db8ff; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: var(--ink); background: radial-gradient(circle at 83% -10%, #1d315e 0, transparent 31rem), radial-gradient(circle at 2% 100%, #103d40 0, transparent 36rem), var(--bg); }
        main { width: min(1280px, calc(100% - 40px)); margin: 0 auto; padding: 32px 0 64px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding: 0 0 28px; border-bottom: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-mark { display: grid; width: 38px; height: 38px; place-items: center; border: 1px solid var(--accent); border-radius: 10px; color: var(--accent); font-weight: 800; box-shadow: 0 0 20px #36d8ae40; }
        .brand h1 { margin: 0; font-size: 1.1rem; letter-spacing: -.02em; } .brand p { margin: 2px 0 0; color: var(--muted); font-size: .78rem; }
        .system-status { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: .78rem; } .system-status::before { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 12px var(--accent); content: ""; }
        .intro { display: flex; justify-content: space-between; align-items: end; gap: 30px; padding: 56px 0 34px; }
        .intro h2 { max-width: 720px; margin: 0; font-size: 2.75rem; line-height: 1.02; letter-spacing: -.035em; text-wrap: balance; }
        .intro p { max-width: 43ch; margin: 0; color: var(--muted); line-height: 1.6; }
        .workspace { display: grid; grid-template-columns: minmax(290px, .8fr) minmax(0, 1.7fr); gap: 22px; align-items: start; }
        .panel { background: linear-gradient(145deg, #141c2e, #101625); border: 1px solid var(--line); border-radius: 14px; overflow: hidden; }
        .panel-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 20px 22px; border-bottom: 1px solid var(--line); }
        .panel h3 { margin: 0; font-size: .95rem; letter-spacing: -.01em; } .panel-header span { color: var(--muted); font-size: .75rem; }
        .form-panel form { display: grid; gap: 16px; padding: 22px; }
        label { display: grid; gap: 7px; color: var(--muted); font-size: .76rem; font-weight: 700; letter-spacing: .015em; }
        input, select { width: 100%; min-height: 43px; border: 1px solid #3a4862; border-radius: 8px; outline: none; background: #0b101c; color: var(--ink); padding: 9px 11px; font: inherit; transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease; }
        input::placeholder { color: #8190aa; } input:hover, select:hover { border-color: #566784; } input:focus, select:focus { border-color: var(--focus); box-shadow: 0 0 0 3px #9db8ff2b; background: #0e1421; }
        .split-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        button, .button { display: inline-flex; align-items: center; justify-content: center; min-height: 39px; border: 1px solid transparent; border-radius: 8px; padding: 8px 12px; font: inherit; font-size: .8rem; font-weight: 750; text-decoration: none; cursor: pointer; transition: transform 180ms ease, background-color 180ms ease, border-color 180ms ease; }
        button:focus-visible, .button:focus-visible { outline: 3px solid var(--focus); outline-offset: 2px; } button:hover, .button:hover { transform: translateY(-1px); }
        .save { width: 100%; background: var(--accent); color: #052119; } .save:hover { background: #98f8d8; } .save::after { margin-left: 8px; content: "→"; }
        .inventory-meta { display: flex; align-items: center; gap: 8px; } .inventory-meta strong { display: grid; min-width: 26px; height: 26px; place-items: center; border-radius: 50%; background: #203250; color: #c7dcff; font-size: .72rem; }
        .table-wrap { overflow-x: auto; } table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th, td { padding: 15px 18px; border-bottom: 1px solid #233048; text-align: left; } th { color: #91a2bf; font-size: .7rem; font-weight: 750; letter-spacing: .08em; text-transform: uppercase; } td { font-size: .86rem; } tbody tr { transition: background-color 160ms ease; } tbody tr:hover { background: #182238; }
        .product-name { color: #fff; font-weight: 700; } .product-id { color: #8291a9; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .76rem; } .category { display: inline-flex; padding: 4px 8px; border-radius: 999px; background: #233454; color: #c1d6ff; font-size: .72rem; font-weight: 700; }
        .price { color: var(--accent); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 700; } .stock { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; } .actions { display: flex; align-items: center; gap: 7px; } .button { min-height: 33px; border-color: #40516e; background: transparent; color: #dce8ff; } .button:hover { border-color: var(--focus); background: #22314d; }
        .actions form { margin: 0; } .btn-delete { min-height: 33px; background: transparent; border-color: #754050; color: #ffadba; } .btn-delete:hover { background: #3a1824; border-color: var(--danger); }
        .empty { padding: 56px 20px; color: var(--muted); text-align: center; } .empty::before { display: block; margin-bottom: 9px; color: var(--accent); font-family: ui-monospace, monospace; font-size: 1rem; content: "[ INVENTARIO VACÍO ]"; }
        @media (max-width: 900px) { .workspace { grid-template-columns: 1fr; } .intro { align-items: start; flex-direction: column; } }
        @media (max-width: 580px) { main { width: min(100% - 28px, 1280px); padding-top: 20px; } .topbar { align-items: flex-start; flex-direction: column; padding-bottom: 20px; } .intro { padding: 36px 0 26px; } .intro h2 { font-size: 2.2rem; } .split-fields { grid-template-columns: 1fr; } .system-status { display: none; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
<main>
    <header class="topbar">
        <div class="brand"><div class="brand-mark">T</div><div><h1>TechStock</h1><p>Control de activos tecnológicos</p></div></div>
        <div class="system-status">Sistema operativo</div>
    </header>
    <section class="intro"><h2>Inventario diseñado para operar a máxima velocidad.</h2><p>Registra, consulta y actualiza cada activo de tu ecosistema tecnológico desde una sola consola.</p></section>
    <div class="workspace">
    <section class="panel form-panel">
        <div class="panel-header"><h3>Nuevo activo</h3><span>REGISTRO</span></div>
        <form method="POST">
            <label>Nombre del producto<input type="text" name="nombre" placeholder="Ej. Laptop Pro 14" maxlength="120" required></label>
            <div class="split-fields"><label>Categoría<select name="categoria" required><option value="">Selecciona</option><option>Computadoras</option><option>Smartphones</option><option>Audio</option><option>Accesorios</option><option>Componentes</option><option>Gaming</option></select></label><label>Marca<input type="text" name="marca" placeholder="Ej. NovaTech" maxlength="80" required></label></div>
            <div class="split-fields"><label>Precio (MXN)<input type="number" name="precio" min="0" step="0.01" placeholder="0.00" required></label><label>Existencias<input type="number" name="stock" min="0" step="1" placeholder="0" required></label></div>
            <button class="save" type="submit" name="agregar">Registrar activo</button>
        </form>
    </section>
    <section class="panel">
    <div class="panel-header"><h3>Inventario en tiempo real</h3><div class="inventory-meta"><strong><?= count($productos) ?></strong><span>ACTIVOS</span></div></div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$productos): ?>
            <tr><td class="empty" colspan="7">Aún no hay productos registrados.</td></tr>
            <?php endif; ?>
            <?php foreach ($productos as $producto): ?>
            <tr>
                <td class="product-id">#<?= htmlspecialchars($producto['id']) ?></td>
                <td class="product-name"><?= htmlspecialchars($producto['nombre']) ?></td>
                <td><span class="category"><?= htmlspecialchars($producto['categoria']) ?></span></td>
                <td><?= htmlspecialchars($producto['marca']) ?></td>
                <td class="price">$<?= number_format((float) $producto['precio'], 2) ?></td>
                <td class="stock"><?= htmlspecialchars($producto['stock']) ?></td>
                <td class="actions">
                    <a class="button" href="editar.php?id=<?= $producto['id'] ?>">Editar</a>
                    <form method="POST" onsubmit="return confirm('¿Eliminar este producto del inventario?')">
                        <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                        <button class="btn-delete" type="submit" name="borrar">Borrar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </section>
    </div>
</main>

</body>
</html>
