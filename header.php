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
<header class="topo">
    <div class="brand">
        <span class="logo">🔐</span>
        <div>
            <p class="logo-sub">SafeShare</p>
            <p class="logo-title">Cofres Compartilhados</p>
        </div>
    </div>
    <nav class="menu">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="index.php?page=cofres">Cofres</a>
            <a href="index.php?page=etiquetas">Etiquetas</a>
            <a class="sair" href="index.php?page=logout">Sair</a>
        <?php else: ?>
            <a href="index.php?page=login">Login</a>
            <a class="destaque" href="index.php?page=register">Registrar</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
