<?php
require_once __DIR__ . '/config.php';

// Cria uma conexão global simples usando mysqli procedural
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die('Erro ao conectar ao MySQL: ' . mysqli_connect_error());
}
?>
