<?php
// Conexión PDO con errores como excepciones y consultas preparadas reales
function conectar(): PDO
{
    $c = require '/var/www/config/db.php';
    $dsn = "mysql:host={$c['host']};dbname={$c['db']};charset={$c['charset']}";
    return new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

// Sesión + token CSRF para proteger los formularios
session_start();
function csrf(): string { return $_SESSION['t'] ??= bin2hex(random_bytes(16)); }
function csrf_ok(): bool { return hash_equals($_SESSION['t'] ?? '', (string)($_POST['t'] ?? '')); }
