<?php
require 'conexion.php';

// Obtener término de búsqueda si existe para exportar los productos filtrados o todos
$busqueda = isset($_GET['buscar']) && is_string($_GET['buscar']) ? trim($_GET['buscar']) : '';

if ($busqueda !== '') {
    $termino = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $busqueda) . '%';
    $stmt = $pdo->prepare("SELECT * FROM productos_tecnologicos WHERE nombre LIKE ? ESCAPE '!' OR categoria LIKE ? ESCAPE '!' OR marca LIKE ? ESCAPE '!' ORDER BY id DESC");
    $stmt->execute([$termino, $termino, $termino]);
} else {
    $stmt = $pdo->query('SELECT * FROM productos_tecnologicos ORDER BY id DESC');
}

$productos = $stmt->fetchAll();

// Generar nombre de archivo con fecha y hora
$filename = 'inventario_productos_' . date('Y-m-d_H-i') . '.csv';

// Configurar encabezados HTTP para la descarga del archivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Abrir el puntero de salida
$output = fopen('php://output', 'w');

// Escribir el BOM UTF-8 (EF BB BF) para asegurar compatibilidad con Excel (acentos, eñes, etc.)
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Cabeceras de columnas en el CSV
fputcsv($output, ['ID', 'Producto', 'Categoría', 'Marca', 'Precio (MXN)', 'Stock', 'Fecha de Registro']);

// Filas de datos
foreach ($productos as $producto) {
    fputcsv($output, [
        $producto['id'],
        $producto['nombre'],
        $producto['categoria'],
        $producto['marca'],
        number_format((float) $producto['precio'], 2, '.', ''),
        $producto['stock'],
        $producto['creado_en'] ?? ''
    ]);
}

fclose($output);
exit;
