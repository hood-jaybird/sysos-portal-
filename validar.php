<?php
// Validación del servidor compartida por guardar.php y editar.php
function validar(array $p): array
{
    $d = [
     'nombres' => trim($p['nombres'] ?? ''), 'apellidos' => trim($p['apellidos'] ?? ''),
     'dpi' => trim($p['dpi'] ?? ''), 'fecha_nacimiento' => trim($p['fecha_nacimiento'] ?? ''),
     'genero' => $p['genero'] ?? '', 'telefono' => trim($p['telefono'] ?? ''),
     'correo' => trim($p['correo'] ?? ''),
    ];
    $e = [];
    if ($d['nombres'] === '' || mb_strlen($d['nombres']) > 80) $e[] = 'Los nombres son obligatorios (máximo 80 caracteres).';
    if ($d['apellidos'] === '' || mb_strlen($d['apellidos']) > 80) $e[] = 'Los apellidos son obligatorios (máximo 80 caracteres).';
    if (!preg_match('/^\d{13}$/', $d['dpi'])) $e[] = 'El DPI debe tener exactamente 13 dígitos.';
    $f = DateTime::createFromFormat('Y-m-d', $d['fecha_nacimiento']);
    if (!$f || $f->format('Y-m-d') !== $d['fecha_nacimiento'] || $f > new DateTime()) $e[] = 'La fecha de nacimiento no es válida.';
    if (!in_array($d['genero'], ['M', 'F', 'Otro'], true)) $e[] = 'Selecciona un género válido.';
    if ($d['telefono'] !== '' && !preg_match('/^[0-9 +-]{8,15}$/', $d['telefono'])) $e[] = 'El teléfono no es válido.';
    if (!filter_var($d['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['correo']) > 100) $e[] = 'El correo electrónico no tiene un formato válido.';
    return [$d, $e];
}
