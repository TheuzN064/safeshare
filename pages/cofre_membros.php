<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$cofreId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$mensagem = '';

// Verifica se usuário é membro ou dono
$acesso = mysqli_query($conn, "SELECT c.*, mc.papel FROM cofres c LEFT JOIN membros_cofre mc ON mc.id_cofre=c.id AND mc.id_usuario=$usuarioId WHERE c.id=$cofreId");
$cofre = $acesso ? mysqli_fetch_assoc($acesso) : null;

if (!$cofre || (!$cofre['papel'] && $cofre['id_dono'] != $usuarioId)) {
    echo '<div class="alerta">Acesso negado.</div>';
    return;
}

// Adicionar membro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_membro'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $papel = mysqli_real_escape_string($conn, $_POST['papel']);

    $u = mysqli_query($conn, "SELECT id FROM usuarios WHERE email='$email' LIMIT 1");
    if ($u && mysqli_num_rows($u) === 1) {
        $dados = mysqli_fetch_assoc($u);
        $novoId = $dados['id'];
        // Evita duplicidade
        $ja = mysqli_query($conn, "SELECT id FROM membros_cofre WHERE id_cofre=$cofreId AND id_usuario=$novoId LIMIT 1");
        if ($ja && mysqli_num_rows($ja) > 0) {
            $mensagem = 'Usuário já é membro deste cofre.';
        } else {
            mysqli_query($conn, "INSERT INTO membros_cofre (id_cofre, id_usuario, papel) VALUES ($cofreId, $novoId, '$papel')");
            $mensagem = 'Membro adicionado.';
        }
    } else {
        $mensagem = 'Usuário não encontrado.';
    }
}

// Remover membro
if (isset($_GET['remover'])) {
    $idRemover = (int) $_GET['remover'];

    // Conta owners restantes
    $owners = mysqli_query($conn, "SELECT id FROM membros_cofre WHERE id_cofre=$cofreId AND papel='OWNER'");
    if ($owners && mysqli_num_rows($owners) <= 1) {
        $mensagem = 'Não é possível remover o último OWNER.';
    } else {
        mysqli_query($conn, "DELETE FROM membros_cofre WHERE id=$idRemover AND id_cofre=$cofreId");
        $mensagem = 'Membro removido.';
    }
}

// Consulta atualizada de owners para exibição
$owners = mysqli_query($conn, "SELECT id FROM membros_cofre WHERE id_cofre=$cofreId AND papel='OWNER'");
$ownerCount = $owners ? mysqli_num_rows($owners) : 0;

$membros = mysqli_query($conn, "SELECT mc.id, u.nome, u.email, mc.papel FROM membros_cofre mc JOIN usuarios u ON u.id = mc.id_usuario WHERE mc.id_cofre=$cofreId");
?>
<h2>Membros do Cofre: <?php echo htmlspecialchars($cofre['nome']); ?></h2>
<?php if ($mensagem): ?><div class="alerta"><?php echo $mensagem; ?></div><?php endif; ?>

<table>
    <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Papel</th>
        <th>Ações</th>
    </tr>
    <?php if ($membros && mysqli_num_rows($membros) > 0): ?>
        <?php while ($m = mysqli_fetch_assoc($membros)): ?>
            <tr>
                <td><?php echo htmlspecialchars($m['nome']); ?></td>
                <td><?php echo htmlspecialchars($m['email']); ?></td>
                <td><?php echo $m['papel']; ?></td>
                <td>
                    <?php if ($m['papel'] !== 'OWNER' || $ownerCount > 1): ?>
                        <a class="btn perigo" href="index.php?page=cofre_membros&id=<?php echo $cofreId; ?>&remover=<?php echo $m['id']; ?>" onclick="return confirm('Remover membro?');">Remover</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">Nenhum membro.</td></tr>
    <?php endif; ?>
</table>

<div class="box">
    <h3>Adicionar membro</h3>
    <form method="post">
        <input type="hidden" name="novo_membro" value="1">
        <label>Email do usuário:</label>
        <input type="email" name="email" required>
        <label>Papel:</label>
        <select name="papel">
            <option value="OWNER">OWNER</option>
            <option value="EDITOR">EDITOR</option>
            <option value="VIEWER">VIEWER</option>
        </select>
        <button type="submit">Adicionar</button>
    </form>
</div>
