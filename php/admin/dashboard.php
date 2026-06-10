<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pdo = getDB();

$totalEstabelecimentos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'estabelecimento' AND ativo = 1")->fetchColumn();
$totalFuncionarios     = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'funcionario' AND ativo = 1")->fetchColumn();
$totalUsuarios         = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'usuario_final' AND ativo = 1")->fetchColumn();
$totalGeral            = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE ativo = 1")->fetchColumn();

$ultimosCadastros = $pdo->query("
    SELECT nome, email, tipo_usuario, criado_em
    FROM usuarios
    WHERE ativo = 1
    ORDER BY criado_em DESC
    LIMIT 8
")->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#1565C0,#42A5F5);">
            <i class="bi bi-people-fill stat-icon"></i>
            <div>
                <div class="stat-value"><?= $totalGeral ?></div>
                <div class="stat-label">Total de Usuários</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#2E7D32,#66BB6A);">
            <i class="bi bi-shop stat-icon"></i>
            <div>
                <div class="stat-value"><?= $totalEstabelecimentos ?></div>
                <div class="stat-label">Estabelecimentos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#6A1B9A,#AB47BC);">
            <i class="bi bi-person-badge stat-icon"></i>
            <div>
                <div class="stat-value"><?= $totalFuncionarios ?></div>
                <div class="stat-label">Funcionários</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#0288D1,#4FC3F7);">
            <i class="bi bi-person-circle stat-icon"></i>
            <div>
                <div class="stat-value"><?= $totalUsuarios ?></div>
                <div class="stat-label">Usuários Finais</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Últimos Cadastros</h6>
        <a href="estabelecimentos.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Cadastrado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimosCadastros)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Nenhum usuário cadastrado ainda.</td></tr>
                    <?php else: foreach ($ultimosCadastros as $u): ?>
                    <tr>
                        <td class="ps-4 fw-semibold"><?= htmlspecialchars($u['nome']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge-tipo badge-<?= $u['tipo_usuario'] ?>">
                                <?php
                                $labels = [
                                    'estabelecimento' => '🏪 Estabelecimento',
                                    'funcionario'     => '👤 Funcionário',
                                    'usuario_final'   => '👥 Usuário Final',
                                ];
                                echo $labels[$u['tipo_usuario']] ?? $u['tipo_usuario'];
                                ?>
                            </span>
                        </td>
                        <td class="text-muted small">
                            <?= date('d/m/Y H:i', strtotime($u['criado_em'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
