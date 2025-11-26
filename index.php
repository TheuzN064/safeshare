<?php
// Configuração de conexão (ajuste usuário/senha do MySQL conforme seu ambiente)
$dbHost = 'localhost';
$dbName = 'vault_db';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Erro ao conectar ao banco: ' . $e->getMessage());
}

// Usuário fixo para demonstração (id 1 criado em database.sql)
$userId = 1;

// Processamento de formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_login') {
        $stmt = $pdo->prepare('INSERT INTO logins (id_usuario, id_categoria, site_nome, site_url, login, senha) VALUES (:user, :cat, :nome, :url, :login, :senha)');
        $stmt->execute([
            ':user'  => $userId,
            ':cat'   => $_POST['id_categoria'] ?? null,
            ':nome'  => $_POST['site_nome'] ?? '',
            ':url'   => $_POST['site_url'] ?? '',
            ':login' => $_POST['login'] ?? '',
            ':senha' => $_POST['senha'] ?? '', // senha em texto puro para fins didáticos
        ]);
    } elseif ($action === 'add_card') {
        $stmt = $pdo->prepare('INSERT INTO cartoes (id_usuario, titular, numero, validade, cvv, bandeira) VALUES (:user, :titular, :numero, :validade, :cvv, :bandeira)');
        $stmt->execute([
            ':user'     => $userId,
            ':titular'  => $_POST['titular'] ?? '',
            ':numero'   => $_POST['numero'] ?? '',
            ':validade' => $_POST['validade'] ?? '',
            ':cvv'      => $_POST['cvv'] ?? '',
            ':bandeira' => $_POST['bandeira'] ?? '',
        ]);
    } elseif ($action === 'delete_login' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM logins WHERE id = :id AND id_usuario = :user');
        $stmt->execute([':id' => (int) $_POST['id'], ':user' => $userId]);
    } elseif ($action === 'delete_card' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM cartoes WHERE id = :id AND id_usuario = :user');
        $stmt->execute([':id' => (int) $_POST['id'], ':user' => $userId]);
    }

    header('Location: index.php');
    exit;
}

// Consultas para exibição
$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();

$logins = $pdo->query('SELECT l.*, c.nome AS categoria_nome, c.cor_hex FROM logins l INNER JOIN categorias c ON l.id_categoria = c.id WHERE l.id_usuario = ' . (int) $userId . ' ORDER BY l.id DESC')->fetchAll();

$cartoes = $pdo->query('SELECT * FROM cartoes WHERE id_usuario = ' . (int) $userId . ' ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vault - Gerenciador de Senhas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <h1>🔒 Vault</h1>
            <nav class="menu">
                <a class="active" href="#senhas">Senhas</a>
                <a href="#cartoes">Cartões</a>
                <a href="#">Relatórios</a>
            </nav>
        </aside>
        <main class="main">
            <header class="page-header">
                <div>
                    <p class="muted">Bem-vindo de volta</p>
                    <p class="page-title">Painel de Cofre Digital</p>
                </div>
                <div class="actions">
                    <button onclick="openModal('modal-login')">+ Nova Senha</button>
                    <button onclick="openModal('modal-card')">+ Novo Cartão</button>
                </div>
            </header>

            <section id="senhas" class="table-card">
                <h2>Senhas</h2>
                <?php if (empty($logins)): ?>
                    <p class="empty">Nenhum login cadastrado. Clique em "Nova Senha" para começar.</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Site</th>
                            <th>Categoria</th>
                            <th>Login</th>
                            <th>Senha</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logins as $login): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($login['site_nome']); ?></strong><br>
                                    <small class="muted"><?php echo htmlspecialchars($login['site_url']); ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="badge">
                                    <span class="dot" style="background: <?php echo htmlspecialchars($login['cor_hex']); ?>"></span>
                                    <?php echo htmlspecialchars($login['categoria_nome']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($login['login']); ?></td>
                            <td>
                                <div class="password-field">
                                    <input type="password" id="pwd-<?php echo $login['id']; ?>" value="<?php echo htmlspecialchars($login['senha']); ?>" readonly>
                                    <button class="icon-btn" onclick="togglePassword('pwd-<?php echo $login['id']; ?>', this)">👁</button>
                                    <button class="icon-btn" onclick="copyPassword('pwd-<?php echo $login['id']; ?>')">📋</button>
                                </div>
                            </td>
                            <td>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Excluir este login?');">
                                    <input type="hidden" name="action" value="delete_login">
                                    <input type="hidden" name="id" value="<?php echo $login['id']; ?>">
                                    <button class="icon-btn danger" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </section>

            <section id="cartoes" class="table-card">
                <h2>Cartões</h2>
                <?php if (empty($cartoes)): ?>
                    <p class="empty">Nenhum cartão cadastrado. Clique em "Novo Cartão" para adicionar.</p>
                <?php else: ?>
                <div class="grid-cards">
                    <?php foreach ($cartoes as $card): ?>
                        <div class="credit-card">
                            <div class="row">
                                <strong><?php echo htmlspecialchars($card['bandeira']); ?></strong>
                                <span><?php echo htmlspecialchars($card['titular']); ?></span>
                            </div>
                            <div class="card-number"><?php echo htmlspecialchars($card['numero']); ?></div>
                            <div class="card-meta">
                                <span>Val: <?php echo htmlspecialchars($card['validade']); ?></span>
                                <span>CVV: <?php echo htmlspecialchars($card['cvv']); ?></span>
                            </div>
                            <div class="card-actions">
                                <form method="post" onsubmit="return confirm('Excluir este cartão?');">
                                    <input type="hidden" name="action" value="delete_card">
                                    <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                    <button class="icon-btn danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <!-- Modal Nova Senha -->
    <div class="modal" id="modal-login">
        <div class="modal-content">
            <h3>Adicionar Novo Login</h3>
            <form method="post">
                <input type="hidden" name="action" value="add_login">
                <div class="form-grid">
                    <div>
                        <label for="site_nome">Nome do site</label>
                        <input required type="text" id="site_nome" name="site_nome" placeholder="Ex: Twitter">
                    </div>
                    <div>
                        <label for="site_url">URL</label>
                        <input type="url" id="site_url" name="site_url" placeholder="https://">
                    </div>
                    <div>
                        <label for="login">Login</label>
                        <input required type="text" id="login" name="login" placeholder="seu@email.com">
                    </div>
                    <div>
                        <label for="senha">Senha (texto puro)</label>
                        <input required type="text" id="senha" name="senha" placeholder="senha123">
                    </div>
                    <div>
                        <label for="id_categoria">Categoria</label>
                        <select required id="id_categoria" name="id_categoria">
                            <option value="" disabled selected>Escolha...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="icon-btn" onclick="closeModal('modal-login')">Cancelar</button>
                    <button type="submit">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Novo Cartão -->
    <div class="modal" id="modal-card">
        <div class="modal-content">
            <h3>Adicionar Novo Cartão</h3>
            <form method="post">
                <input type="hidden" name="action" value="add_card">
                <div class="form-grid">
                    <div>
                        <label for="titular">Titular</label>
                        <input required type="text" id="titular" name="titular" placeholder="Nome impresso">
                    </div>
                    <div>
                        <label for="numero">Número</label>
                        <input required type="text" id="numero" name="numero" placeholder="0000 0000 0000 0000">
                    </div>
                    <div>
                        <label for="validade">Validade (MM/AA)</label>
                        <input required type="text" id="validade" name="validade" placeholder="12/28">
                    </div>
                    <div>
                        <label for="cvv">CVV</label>
                        <input required type="text" id="cvv" name="cvv" placeholder="123">
                    </div>
                    <div>
                        <label for="bandeira">Bandeira</label>
                        <input required type="text" id="bandeira" name="bandeira" placeholder="Visa / Master">
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="icon-btn" onclick="closeModal('modal-card')">Cancelar</button>
                    <button type="submit">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }

        function copyPassword(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            navigator.clipboard.writeText(input.value).then(() => {
                alert('Senha copiada!');
            }).catch(() => {
                alert('Não foi possível copiar.');
            });
        }

        function openModal(id) {
            document.getElementById(id)?.classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id)?.classList.remove('open');
        }

        document.addEventListener('keydown', (ev) => {
            if (ev.key === 'Escape') {
                document.querySelectorAll('.modal.open').forEach(m => m.classList.remove('open'));
            }
        });
    </script>
</body>
</html>
