<?php
require_once 'db.php';
require_once 'header.php';

$page = isset($_GET['page']) ? $_GET['page'] : '';

// Redireciona conforme login quando nenhuma página foi escolhida
if ($page === '') {
    if (isset($_SESSION['usuario_id'])) {
        header('Location: index.php?page=cofres');
        exit;
    } else {
        header('Location: index.php?page=login');
        exit;
    }
}

// Logout simples limpando a sessão
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

$permitidas = ['login','register','cofres','cofre_membros','cofre_senhas','etiquetas'];

if (in_array($page, $permitidas)) {
    include __DIR__ . '/pages/' . $page . '.php';
} else {
    echo '<p>Página não encontrada.</p>';
}

require_once 'footer.php';
?>
