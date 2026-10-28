<?php
// Verifica CSRF, valida en el servidor e inserta con consulta preparada
require __DIR__ . '/conexion.php';
require __DIR__ . '/validar.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
if (!csrf_ok()) { http_response_code(403); exit('Token de seguridad inválido. Recarga el formulario.'); }

[$d, $errores] = validar($_POST);
if (!$errores) {
    $d['telefono'] = $d['telefono'] !== '' ? $d['telefono'] : null;
    $sql = 'INSERT INTO personas (nombres, apellidos, dpi, fecha_nacimiento, genero, telefono, correo)
            VALUES (:nombres, :apellidos, :dpi, :fecha_nacimiento, :genero, :telefono, :correo)';
    try {
        conectar()->prepare($sql)->execute($d);
        header('Location: listar.php?ok=1'); // POST-Redirect-GET
        exit;
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) === 1062) {
            $errores[] = 'Ya existe una persona registrada con ese DPI o correo.';
        } else {
            error_log('registro: ' . $e->getMessage());
            $errores[] = 'No se pudo guardar el registro. Intenta más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Registro de personas</title><link rel="stylesheet" href="estilos.css"></head>
<body><main class="card">
 <div class="error"><strong>No se pudo guardar el registro:</strong>
  <ul><?php foreach ($errores as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
 <a class="btn" href="index.php">&larr; Volver al formulario</a>
</main></body></html>
