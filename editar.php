<?php
// Edición: GET muestra el formulario con los datos; POST valida y hace UPDATE preparado
require __DIR__ . '/conexion.php';
require __DIR__ . '/validar.php';
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$errores = []; $p = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { http_response_code(403); exit('Token de seguridad inválido.'); }
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    [$d, $errores] = validar($_POST);
    $p = $d + ['id' => $id];
    if (!$errores && $id) {
        $d['telefono'] = $d['telefono'] !== '' ? $d['telefono'] : null;
        $sql = 'UPDATE personas SET nombres=:nombres, apellidos=:apellidos, dpi=:dpi, fecha_nacimiento=:fecha_nacimiento,
                genero=:genero, telefono=:telefono, correo=:correo WHERE id=:id';
        try { conectar()->prepare($sql)->execute($d + ['id' => $id]); header('Location: listar.php?ed=1'); exit; }
        catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1062) $errores[] = 'Ya existe otra persona con ese DPI o correo.';
            else { error_log('registro: ' . $e->getMessage()); $errores[] = 'No se pudo actualizar el registro.'; }
        }
    }
} else {
    $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
    if ($id) { $s = conectar()->prepare('SELECT * FROM personas WHERE id = ?'); $s->execute([$id]); $p = $s->fetch() ?: null; }
}
if (!$p) { http_response_code(404); exit('Registro no encontrado'); }
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Editar persona</title><link rel="stylesheet" href="estilos.css"></head>
<body><main class="card">
 <h1>Editar persona</h1>
 <?php if ($errores): ?><div class="error"><ul><?php foreach ($errores as $e): ?><li><?= $h($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
 <form method="post" action="editar.php">
  <input type="hidden" name="t" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
  <div class="grid">
   <label>Nombres *<input name="nombres" required maxlength="80" value="<?= $h($p['nombres']) ?>"></label>
   <label>Apellidos *<input name="apellidos" required maxlength="80" value="<?= $h($p['apellidos']) ?>"></label>
   <label>DPI (13 dígitos) *<input name="dpi" required maxlength="13" pattern="\d{13}" value="<?= $h($p['dpi']) ?>"></label>
   <label>Fecha de nacimiento *<input type="date" name="fecha_nacimiento" required value="<?= $h($p['fecha_nacimiento']) ?>"></label>
   <label>Género *<select name="genero" required>
    <?php foreach (['M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro'] as $k => $v): ?>
     <option value="<?= $k ?>" <?= $p['genero'] === $k ? 'selected' : '' ?>><?= $v ?></option>
    <?php endforeach; ?></select></label>
   <label>Teléfono<input name="telefono" maxlength="15" value="<?= $h($p['telefono']) ?>"></label>
  </div>
  <label>Correo electrónico *<input type="email" name="correo" required maxlength="100" value="<?= $h($p['correo']) ?>"></label>
  <div class="acts"><button class="btn" type="submit">Guardar cambios</button><a href="listar.php">Cancelar</a></div>
 </form>
</main></body></html>
