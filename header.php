<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>SafeShare</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<div class="topo">
    <h1>SafeShare - Gerenciador de Cofres Compartilhados</h1>
    <nav>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="index.php?page=cofres">Cofres</a>
            <a href="index.php?page=etiquetas">Etiquetas</a>
            <a href="index.php?page=logout">Sair</a>
        <?php else: ?>
            <a href="index.php?page=login">Login</a>
            <a href="index.php?page=register">Registrar</a>
        <?php endif; ?>
    </nav>
</div>
<div class="container">
