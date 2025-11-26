<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$cofreId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$mensagem = '';

// Verifica se usuário é membro do cofre
$acesso = mysqli_query($conn, "SELECT c.nome, c.id_dono, mc.papel FROM cofres c LEFT JOIN membros_cofre mc ON mc.id_cofre=c.id AND mc.id_usuario=$usuarioId WHERE c.id=$cofreId");
$cofre = $acesso ? mysqli_fetch_assoc($acesso) : null;

$podeEditar = $cofre && (($cofre['id_dono'] == $usuarioId) || ($cofre['papel'] === 'OWNER') || ($cofre['papel'] === 'EDITOR'));

if (!$cofre || (!$cofre['papel'] && $cofre['id_dono'] != $usuarioId)) {
    echo '<div class="alerta">Acesso negado.</div>';
    return;
}

// Adicionar nova senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_senha'])) {
    if (!$podeEditar) {
        $mensagem = 'Somente owners ou editores podem adicionar senhas.';
    } else {
        $rotulo = mysqli_real_escape_string($conn, $_POST['rotulo']);
        $usuarioLogin = mysqli_real_escape_string($conn, $_POST['usuario_login']);
        $senhaValor = mysqli_real_escape_string($conn, $_POST['senha_valor']);
        $notas = mysqli_real_escape_string($conn, $_POST['notas']);

        $sql = "INSERT INTO senhas (id_cofre, rotulo, usuario_login, senha_valor, notas) VALUES ($cofreId, '$rotulo', '$usuarioLogin', '$senhaValor', '$notas')";
        if (mysqli_query($conn, $sql)) {
            $senhaId = mysqli_insert_id($conn);
            if (isset($_POST['etiquetas']) && is_array($_POST['etiquetas'])) {
                foreach ($_POST['etiquetas'] as $et) {
                    $etId = (int) $et;
                    mysqli_query($conn, "INSERT INTO senha_etiqueta (id_senha, id_etiqueta) VALUES ($senhaId, $etId)");
                }
            }
            $mensagem = 'Senha adicionada.';
        } else {
            $mensagem = 'Erro ao adicionar senha: ' . mysqli_error($conn);
        }
    }
}

// Excluir senha
if (isset($_GET['excluir_senha'])) {
    if (!$podeEditar) {
        $mensagem = 'Somente owners ou editores podem excluir senhas.';
    } else {
        $senhaId = (int) $_GET['excluir_senha'];
        mysqli_query($conn, "DELETE FROM senha_etiqueta WHERE id_senha=$senhaId");
        mysqli_query($conn, "DELETE FROM senhas WHERE id=$senhaId AND id_cofre=$cofreId");
        $mensagem = 'Senha excluída.';
    }
}

// Busca etiquetas disponíveis
$etiquetas = mysqli_query($conn, "SELECT * FROM etiquetas ORDER BY nome");

// Lista de senhas do cofre com etiquetas
$senhas = mysqli_query($conn, "SELECT s.* FROM senhas s WHERE s.id_cofre=$cofreId");
?>
<h2>Senhas do Cofre: <?php echo htmlspecialchars($cofre['nome']); ?></h2>
<?php if ($mensagem): ?><div class="alerta"><?php echo $mensagem; ?></div><?php endif; ?>
<?php if (!$podeEditar): ?><div class="info">Você possui acesso de visualização. Apenas owners ou editores podem gerenciar senhas.</div><?php endif; ?>

<table>
    <tr>
        <th>Rótulo</th>
        <th>Usuário</th>
        <th>Senha</th>
        <th>Etiquetas</th>
        <th>Ações</th>
    </tr>
    <?php if ($senhas && mysqli_num_rows($senhas) > 0): ?>
        <?php while ($s = mysqli_fetch_assoc($senhas)): ?>
            <?php
            // Busca etiquetas da senha
            $tags = mysqli_query($conn, "SELECT e.nome FROM senha_etiqueta se JOIN etiquetas e ON e.id = se.id_etiqueta WHERE se.id_senha=" . $s['id']);
            $nomesTags = [];
            if ($tags) {
                while ($t = mysqli_fetch_assoc($tags)) { $nomesTags[] = $t['nome']; }
            }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($s['rotulo']); ?></td>
                <td><?php echo htmlspecialchars($s['usuario_login']); ?></td>
                <td><?php echo htmlspecialchars($s['senha_valor']); ?></td>
                <td><?php echo implode(', ', $nomesTags); ?></td>
                <td>
                    <?php if ($podeEditar): ?>
                        <a class="btn perigo" href="index.php?page=cofre_senhas&id=<?php echo $cofreId; ?>&excluir_senha=<?php echo $s['id']; ?>" onclick="return confirm('Excluir senha?');">Excluir</a>
                    <?php else: ?>
                        <span class="empty">Somente leitura</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" class="empty">Nenhuma senha cadastrada.</td></tr>
    <?php endif; ?>
</table>

<?php if ($podeEditar): ?>
<div class="box">
    <h3>Adicionar nova senha</h3>
    <form method="post">
        <input type="hidden" name="nova_senha" value="1">
        <label>Rótulo:</label>
        <input type="text" name="rotulo" required>
        <label>Usuário de login:</label>
        <input type="text" name="usuario_login" required>
        <label>Senha (texto puro):</label>
        <input type="text" name="senha_valor" required>
        <label>Notas:</label>
        <textarea name="notas"></textarea>
        <label>Etiquetas:</label>
        <div class="checkboxes">
            <?php if ($etiquetas && mysqli_num_rows($etiquetas) > 0): ?>
                <?php while ($e = mysqli_fetch_assoc($etiquetas)): ?>
                    <label class="inline"><input type="checkbox" name="etiquetas[]" value="<?php echo $e['id']; ?>"> <?php echo htmlspecialchars($e['nome']); ?></label>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma etiqueta cadastrada.</p>
            <?php endif; ?>
        </div>
        <button type="submit">Salvar</button>
    </form>
</div>
<?php endif; ?>
