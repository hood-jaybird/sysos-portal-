<?php require __DIR__ . "/conexion.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Registro de personas</title><link rel="stylesheet" href="estilos.css"></head>
<body><main class="card">
 <h1>Registro de personas</h1><p class="sub">Los campos con * son obligatorios</p>
 <form method="post" action="guardar.php"><input type="hidden" name="t" value="<?= csrf() ?>">
  <div class="grid">
   <label>Nombres *<input name="nombres" required maxlength="80"></label>
   <label>Apellidos *<input name="apellidos" required maxlength="80"></label>
   <label>DPI (13 dígitos) *<input name="dpi" required maxlength="13" pattern="\d{13}" inputmode="numeric"></label>
   <label>Fecha de nacimiento *<input type="date" name="fecha_nacimiento" required></label>
   <label>Género *<select name="genero" required><option value="">Selecciona...</option><option value="M">Masculino</option><option value="F">Femenino</option><option value="Otro">Otro</option></select></label>
   <label>Teléfono<input name="telefono" maxlength="15" placeholder="5512-3478"></label>
  </div>
  <label>Correo electrónico *<input type="email" name="correo" required maxlength="100"></label>
  <div class="acts"><button class="btn" type="submit">Guardar registro</button><a href="listar.php">Ver personas registradas &rarr;</a></div>
 </form>
</main></body></html>
