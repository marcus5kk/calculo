<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pdo = getDB();
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        if ($acao === 'desativar') {
            $pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ? AND tipo_usuario = 'usuario_final'")->execute([$id]);
            $sucesso = 'Usuário desativado.';
        } elseif ($acao === 'reativar') {
            $pdo->prepare("UPDATE usuarios SET ativo = 1 WHERE id = ? AND tipo_usuario = 'usuario_final'")->execute([$id]);
            $sucesso = 'Usuário reativado.';
        }
    }
}

$busca = trim($_GET['busca'] ?? '');
$where = "WHERE tipo_usuario = 'usuario_final'";
$params = [];
if ($busca !== '') {
    $where .= " AND (nome LIKE ? OR email LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}

$stmt = $pdo->prepare("SELECT * FROM usuarios $where ORDER BY criado_em DESC");
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

$pageTitle = 'Usuários Finais';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($sucesso): ?>
<div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i><?= htmlspecialchars($sucesso) ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="mb-0 fw-bold">Usuários Finais</h5>
        <p class="text-muted small mb-0">Usuários que se cadastraram pelo app</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Buscar</label>
                <input type="text" name="busca" class="form-control" placeholder="Nome ou e-mail..."
                       value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-fill">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
                <a href="usuarios.php" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">#</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Cadastrado em</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                    Nenhum usuário encontrado.
                </td></tr>
                <?php else: foreach ($usuarios as $u): ?>
                <tr>
                    <td class="ps-4 text-muted small"><?= $u['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($u['nome']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="text-muted small"><?= date('d/m/Y', strtotime($u['criado_em'])) ?></td>
                    <td>
                        <?= $u['ativo']
                            ? '<span class="badge bg-success-subtle text-success fw-semibold">Ativo</span>'
                            : '<span class="badge bg-danger-subtle text-danger fw-semibold">Inativo</span>' ?>
                    </td>
                    <td class="text-end pe-4">
                        <form method="POST" action="" class="d-inline">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <?php if ($u['ativo']): ?>
                            <input type="hidden" name="acao" value="desativar">
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Desativar usuário?')">
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
    <?php if (!empty($usuarios)): ?>
    <div class="card-footer bg-white text-muted small">
        <?= count($usuarios) ?> usuário(s) encontrado(s)
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
