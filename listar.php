<?php
require __DIR__ . '/conexion.php';
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); // evita XSS
$q = trim($_GET['q'] ?? '');
$rows = []; $fallo = false;
try {
    $sql = 'SELECT id,nombres,apellidos,dpi,fecha_nacimiento,genero,telefono,correo,fecha_registro FROM personas';
    $par = [];
    if ($q !== '') { $sql .= ' WHERE apellidos LIKE ? OR dpi LIKE ?'; $par = ["%$q%", "%$q%"]; } // búsqueda preparada
    $s = conectar()->prepare($sql . ' ORDER BY fecha_registro DESC, id DESC');
    $s->execute($par); $rows = $s->fetchAll();
} catch (PDOException $e) { error_log('registro: ' . $e->getMessage()); $fallo = true; }
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Personas registradas</title><link rel="stylesheet" href="estilos.css"></head>
<body><main class="card wide">
 <h1>Personas registradas</h1>
 <?php if (isset($_GET['ok'])): ?><div class="okmsg">Registro guardado correctamente</div><?php endif; ?>
 <?php if (isset($_GET['ed'])): ?><div class="okmsg">Registro actualizado</div><?php endif; ?>
 <?php if (isset($_GET['del'])): ?><div class="okmsg">Registro eliminado</div><?php endif; ?>
 <?php if ($fallo): ?><div class="error">No se pudo cargar el listado.</div><?php endif; ?>
 <form class="busca" method="get"><input name="q" value="<?= $h($q) ?>" placeholder="Buscar por apellido o DPI"><button class="btn" type="submit">Buscar</button><?php if ($q !== ''): ?><a href="listar.php">Limpiar</a><?php endif; ?></form>
 <p class="sub">Total de registros: <strong><?= count($rows) ?></strong><?= $q !== '' ? ' (filtrados)' : '' ?></p>
 <div class="scroll"><table>
  <tr><th>ID</th><th>Nombres</th><th>Apellidos</th><th>DPI</th><th>Nacimiento</th><th>Género</th><th>Teléfono</th><th>Correo</th><th>Registrado</th><th></th></tr>
  <?php foreach ($rows as $r): ?>
  <tr><td><?= $h($r['id']) ?></td><td><?= $h($r['nombres']) ?></td><td><?= $h($r['apellidos']) ?></td><td><?= $h($r['dpi']) ?></td><td><?= $h($r['fecha_nacimiento']) ?></td><td><?= $h($r['genero']) ?></td><td><?= $h($r['telefono']) ?></td><td><?= $h($r['correo']) ?></td><td><?= $h($r['fecha_registro']) ?></td>
  <td class="acc"><a class="btn sm" href="editar.php?id=<?= (int)$r['id'] ?>">Editar</a>
   <form method="post" action="eliminar.php" onsubmit="return confirm('¿Eliminar este registro?')"><input type="hidden" name="t" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="del" type="submit">Eliminar</button></form></td></tr>
  <?php endforeach; ?>
 </table></div>
 <a class="btn" href="index.php">&larr; Nuevo registro</a>
</main></body></html>
