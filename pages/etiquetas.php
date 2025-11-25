<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$mensagem = '';

// Nova etiqueta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_etiqueta'])) {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    if ($nome !== '') {
        $sql = "INSERT INTO etiquetas (nome) VALUES ('$nome')";
        if (!mysqli_query($conn, $sql)) {
            $mensagem = 'Erro ao criar etiqueta: ' . mysqli_error($conn);
        } else {
            $mensagem = 'Etiqueta criada.';
        }
    }
}

// Excluir etiqueta
if (isset($_GET['excluir'])) {
    $idEtiqueta = (int) $_GET['excluir'];
    $uso = mysqli_query($conn, "SELECT id FROM senha_etiqueta WHERE id_etiqueta=$idEtiqueta LIMIT 1");
    if ($uso && mysqli_num_rows($uso) > 0) {
        $mensagem = 'Não é possível excluir esta etiqueta, pois ela está sendo usada em senhas.';
    } else {
        mysqli_query($conn, "DELETE FROM etiquetas WHERE id=$idEtiqueta");
        $mensagem = 'Etiqueta excluída.';
    }
}

$lista = mysqli_query($conn, "SELECT * FROM etiquetas ORDER BY nome");
?>
<h2>Etiquetas</h2>
<?php if ($mensagem): ?><div class="alerta"><?php echo $mensagem; ?></div><?php endif; ?>

<div class="box">
    <h3>Nova etiqueta</h3>
    <form method="post">
        <input type="hidden" name="nova_etiqueta" value="1">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <button type="submit">Salvar</button>
    </form>
</div>

<table>
    <tr>
        <th>Nome</th>
        <th>Ações</th>
    </tr>
    <?php if ($lista && mysqli_num_rows($lista) > 0): ?>
        <?php while ($e = mysqli_fetch_assoc($lista)): ?>
            <tr>
                <td><?php echo htmlspecialchars($e['nome']); ?></td>
                <td>
                    <a class="btn perigo" href="index.php?page=etiquetas&excluir=<?php echo $e['id']; ?>" onclick="return confirm('Excluir etiqueta?');">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="2">Nenhuma etiqueta cadastrada.</td></tr>
    <?php endif; ?>
</table>
