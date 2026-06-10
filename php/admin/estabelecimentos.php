<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pdo = getDB();
$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'criar') {
        $nome  = trim($_POST['nome'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            $erro = 'Preencha todos os campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'E-mail inválido.';
        } elseif (strlen($senha) < 6) {
            $erro = 'A senha deve ter ao menos 6 caracteres.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo)
                    VALUES (?, ?, ?, 'estabelecimento', 1)
                ");
                $stmt->execute([$nome, $email, $hash]);
                $sucesso = "Estabelecimento \"$nome\" criado com sucesso!";
            }
        }
    } elseif ($acao === 'desativar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ? AND tipo_usuario = 'estabelecimento'")->execute([$id]);
            $sucesso = 'Estabelecimento desativado.';
        }
    } elseif ($acao === 'reativar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("UPDATE usuarios SET ativo = 1 WHERE id = ? AND tipo_usuario = 'estabelecimento'")->execute([$id]);
            $sucesso = 'Estabelecimento reativado.';
        }
    }
}

$busca = trim($_GET['busca'] ?? '');
$filtroAtivo = $_GET['ativo'] ?? 'todos';

$where = "WHERE tipo_usuario = 'estabelecimento'";
$params = [];

if ($busca !== '') {
    $where .= " AND (nome LIKE ? OR email LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

if ($filtroAtivo === '1') {
    $where .= " AND ativo = 1";
} elseif ($filtroAtivo === '0') {
    $where .= " AND ativo = 0";
}

$stmt = $pdo->prepare("
    SELECT u.*, (
        SELECT COUNT(*) FROM usuarios f WHERE f.empresa_id = u.id AND f.ativo = 1
    ) as total_funcionarios
    FROM usuarios u
    $where
    ORDER BY u.criado_em DESC
");
$stmt->execute($params);
$estabelecimentos = $stmt->fetchAll();

$pageTitle = 'Estabelecimentos';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($sucesso): ?>
<div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i>
    <?= htmlspecialchars($sucesso) ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($erro): ?>
<div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <?= htmlspecialchars($erro) ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="mb-0 fw-bold">Estabelecimentos</h5>
        <p class="text-muted small mb-0">Gerencie os estabelecimentos que usam o app</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCriar">
        <i class="bi bi-plus-lg me-2"></i>Novo Estabelecimento
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Buscar</label>
                <input type="text" name="busca" class="form-control" placeholder="Nome ou e-mail..."
                       value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="ativo" class="form-select">
                    <option value="todos" <?= $filtroAtivo === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="1" <?= $filtroAtivo === '1' ? 'selected' : '' ?>>Ativos</option>
                    <option value="0" <?= $filtroAtivo === '0' ? 'selected' : '' ?>>Desativados</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-fill">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
                <a href="estabelecimentos.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Estabelecimento</th>
                        <th>E-mail</th>
                        <th>Funcionários</th>
                        <th>Cadastrado em</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($estabelecimentos)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-shop fs-1 d-block mb-2 opacity-25"></i>
                        Nenhum estabelecimento encontrado.
                    </td></tr>
                    <?php else: foreach ($estabelecimentos as $e): ?>
                    <tr>
                        <td class="ps-4 text-muted small"><?= $e['id'] ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($e['nome']) ?></div>
                        </td>
                        <td class="text-muted"><?= htmlspecialchars($e['email']) ?></td>
                        <td>
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-person me-1"></i><?= $e['total_funcionarios'] ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($e['criado_em'])) ?></td>
                        <td>
                            <?php if ($e['ativo']): ?>
                            <span class="badge bg-success-subtle text-success fw-semibold">Ativo</span>
                            <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger fw-semibold">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                <?php if ($e['ativo']): ?>
                                <input type="hidden" name="acao" value="desativar">
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Desativar este estabelecimento?')">
                                    <i class="bi bi-pause-circle"></i>
                                </button>
                                <?php else: ?>
                                <input type="hidden" name="acao" value="reativar">
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-play-circle"></i>
                                </button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($estabelecimentos)): ?>
    <div class="card-footer bg-white text-muted small">
        <?= count($estabelecimentos) ?> estabelecimento(s) encontrado(s)
    </div>
    <?php endif; ?>
</div>

<!-- Modal Criar Estabelecimento -->
<div class="modal fade" id="modalCriar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shop me-2 text-primary"></i>Novo Estabelecimento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="acao" value="criar">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome do estabelecimento</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Salão da Maria" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">E-mail de login</label>
                        <input type="email" name="email" class="form-control" placeholder="email@empresa.com.br" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Senha inicial</label>
                        <input type="password" name="senha" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                        <div class="form-text">O estabelecimento usará essa senha para fazer login no app.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Criar Estabelecimento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
