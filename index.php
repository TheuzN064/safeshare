<?php
require_once __DIR__ . '/db.php';

// Resolve página atual (rotas permitidas)
$allowedPages = ['login', 'register', 'cofres', 'cofre_senhas', 'cofre_membros', 'etiquetas'];
$page = $_GET['page'] ?? '';

if ($page === '') {
    // Redireciona para login ou área logada, conforme sessão
    $page = isset($_SESSION['usuario_id']) ? 'cofres' : 'login';
}

// Finaliza sessão
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

include __DIR__ . '/header.php';

if (!in_array($page, $allowedPages, true)) {
    echo '<div class="alerta">Página não encontrada.</div>';
    include __DIR__ . '/footer.php';
    exit;
}

$pageFile = __DIR__ . "/pages/{$page}.php";

if (file_exists($pageFile)) {
    include $pageFile;
} else {
    echo '<div class="alerta">Arquivo da página não localizado.</div>';
}

include __DIR__ . '/footer.php';
