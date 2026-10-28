<?php
// Elimina un registro (solo POST, con token CSRF y consulta preparada)
require __DIR__ . '/conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: listar.php'); exit; }
if (!csrf_ok()) { http_response_code(403); exit('Token de seguridad inválido.'); }
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if ($id) {
    try { conectar()->prepare('DELETE FROM personas WHERE id = ?')->execute([$id]); }
    catch (PDOException $e) { error_log('registro: ' . $e->getMessage()); }
}
header('Location: listar.php?del=1');
