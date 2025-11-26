<?php
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = mysqli_real_escape_string($conn, $_POST['senha']);
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se email já existe
    $verifica = mysqli_query($conn, "SELECT id FROM usuarios WHERE email='$email' LIMIT 1");
    if ($verifica && mysqli_num_rows($verifica) > 0) {
        $mensagem = 'Email já cadastrado.';
    } else {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senhaHash')";
        if (mysqli_query($conn, $sql)) {
            header('Location: index.php?page=login');
            exit;
        } else {
            $mensagem = 'Erro ao registrar: ' . mysqli_error($conn);
        }
    }
}
?>
<div class="form-card">
    <h2>Registrar</h2>
    <?php if ($mensagem): ?><div class="alerta"><?php echo $mensagem; ?></div><?php endif; ?>
    <form method="post">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <button type="submit">Criar Conta</button>
    </form>
    <p>Já possui conta? <a href="index.php?page=login">Faça login</a></p>
</div>
