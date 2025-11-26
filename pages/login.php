<?php
// Redireciona se já estiver logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=cofres');
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = mysqli_real_escape_string($conn, $_POST['senha']);

    // Busca o usuário com email e senha usando hash seguro
    $sql = "SELECT id, senha FROM usuarios WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $usuario = mysqli_fetch_assoc($result);
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            header('Location: index.php?page=cofres');
            exit;
        }
    }

    $mensagem = 'Login inválido';
}
?>
<div class="form-card">
    <h2>Login</h2>
    <?php if ($mensagem): ?><div class="alerta"><?php echo $mensagem; ?></div><?php endif; ?>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <button type="submit">Entrar</button>
    </form>
    <p>Não tem conta? <a href="index.php?page=register">Registrar</a></p>
</div>
