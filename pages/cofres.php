<?php
// Verifica se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$mensagem = '';

// Criação de novo cofre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_cofre'])) {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);

    $sql = "INSERT INTO cofres (nome, descricao, id_dono) VALUES ('$nome', '$descricao', $usuarioId)";
    if (mysqli_query($conn, $sql)) {
        $cofreId = mysqli_insert_id($conn);
        // Adiciona o dono também como membro OWNER
        mysqli_query($conn, "INSERT INTO membros_cofre (id_cofre, id_usuario, papel) VALUES ($cofreId, $usuarioId, 'OWNER')");
        $mensagem = 'Cofre criado com sucesso!';
    } else {
        $mensagem = 'Erro ao criar cofre: ' . mysqli_error($conn);
    }
}

// Exclusão de cofre (apenas dono)
if (isset($_GET['excluir'])) {
    $idCofre = (int) $_GET['excluir'];

    // Verifica se o usuário é dono
    $donoCheck = mysqli_query($conn, "SELECT id FROM cofres WHERE id=$idCofre AND id_dono=$usuarioId");
    if ($donoCheck && mysqli_num_rows($donoCheck) === 1) {
        // Verifica dependências: senhas ou membros
        $temSenhas = mysqli_query($conn, "SELECT id FROM senhas WHERE id_cofre=$idCofre LIMIT 1");
        $temMembros = mysqli_query($conn, "SELECT id FROM membros_cofre WHERE id_cofre=$idCofre AND id_usuario <> $usuarioId LIMIT 1");

        if (($temSenhas && mysqli_num_rows($temSenhas) > 0) || ($temMembros && mysqli_num_rows($temMembros) > 0)) {
            $mensagem = 'Não é possível excluir este cofre, pois ainda existem senhas e/ou membros vinculados.';
        } else {
            mysqli_query($conn, "DELETE FROM membros_cofre WHERE id_cofre=$idCofre");
            mysqli_query($conn, "DELETE FROM cofres WHERE id=$idCofre");
            $mensagem = 'Cofre excluído com sucesso.';
        }
    } else {
        $mensagem = 'Apenas o dono pode excluir este cofre.';
    }
}

// Lista de cofres onde usuário é dono ou membro
$sql = "SELECT c.*, mc.papel FROM cofres c
        LEFT JOIN membros_cofre mc ON mc.id_cofre = c.id AND mc.id_usuario = $usuarioId
        WHERE c.id_dono = $usuarioId OR mc.id_usuario = $usuarioId
        GROUP BY c.id";
$cofres = mysqli_query($conn, $sql);
?>
<h2>Meus Cofres</h2>
<?php if ($mensagem): ?><div class="alerta"><?php echo $mensagem; ?></div><?php endif; ?>
<div class="box">
    <h3>Criar novo cofre</h3>
    <form method="post">
        <input type="hidden" name="novo_cofre" value="1">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <label>Descrição:</label>
        <textarea name="descricao"></textarea>
        <button type="submit">Salvar</button>
    </form>
</div>

<table>
    <tr>
        <th>Nome</th>
        <th>Descrição</th>
        <th>Papel</th>
        <th>Ações</th>
    </tr>
    <?php if ($cofres && mysqli_num_rows($cofres) > 0): ?>
        <?php while ($c = mysqli_fetch_assoc($cofres)): ?>
            <tr>
                <td><?php echo htmlspecialchars($c['nome']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($c['descricao'])); ?></td>
                <td><?php echo $c['id_dono'] == $usuarioId ? 'OWNER' : $c['papel']; ?></td>
                <td>
                    <a class="btn" href="index.php?page=cofre_senhas&id=<?php echo $c['id']; ?>">Ver Senhas</a>
                    <a class="btn" href="index.php?page=cofre_membros&id=<?php echo $c['id']; ?>">Membros</a>
                    <?php if ($c['id_dono'] == $usuarioId): ?>
                        <a class="btn perigo" href="index.php?page=cofres&excluir=<?php echo $c['id']; ?>" onclick="return confirm('Excluir cofre?');">Excluir</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">Nenhum cofre encontrado.</td></tr>
    <?php endif; ?>
</table>
