<?php
/**
 * AgendaJá — Script de Instalação
 * Acesse este arquivo UMA VEZ pelo navegador após fazer upload para a Hostgator.
 * Ex: https://seu-dominio.com.br/agendaja/php/setup/install.php
 *
 * Após instalar, DELETE ou renomeie este arquivo por segurança.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'marc4901_agendaja');
define('DB_USER', 'marc4901_agendaja');
define('DB_PASS', 'Ma35881706');

$erros = [];
$passos = [];

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $passos[] = ['ok', 'Conexão com o banco de dados estabelecida!'];
} catch (PDOException $e) {
    $erros[] = 'Erro de conexão: ' . $e->getMessage();
    $pdo = null;
}

if ($pdo) {
    try {
        $pdo->exec("SET NAMES utf8mb4");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `usuarios` (
                `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `nome`         VARCHAR(255) NOT NULL,
                `email`        VARCHAR(255) NOT NULL,
                `senha`        VARCHAR(255) NOT NULL,
                `tipo_usuario` ENUM('usuario_final','estabelecimento','funcionario') NOT NULL DEFAULT 'usuario_final',
                `empresa_id`   INT UNSIGNED NULL DEFAULT NULL,
                `ativo`        TINYINT(1) NOT NULL DEFAULT 1,
                `criado_em`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `atualizado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`),
                KEY `empresa_id` (`empresa_id`),
                KEY `tipo_usuario` (`tipo_usuario`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $passos[] = ['ok', 'Tabela <strong>usuarios</strong> criada (ou já existia).'];
    } catch (PDOException $e) {
        $erros[] = 'Erro ao criar tabela usuarios: ' . $e->getMessage();
    }

    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `admins` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `login`     VARCHAR(100) NOT NULL,
                `senha`     VARCHAR(255) NOT NULL,
                `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `login` (`login`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $passos[] = ['ok', 'Tabela <strong>admins</strong> criada (ou já existia).'];
    } catch (PDOException $e) {
        $erros[] = 'Erro ao criar tabela admins: ' . $e->getMessage();
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE login = 'admin'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $hash = password_hash('admin', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO admins (login, senha) VALUES ('admin', ?)")->execute([$hash]);
            $passos[] = ['ok', 'Admin padrão criado: <strong>login=admin / senha=admin</strong>'];
        } else {
            $passos[] = ['info', 'Admin já existe, senha não foi alterada.'];
        }
    } catch (PDOException $e) {
        $erros[] = 'Erro ao criar admin: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendaJá — Instalação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .install-card { max-width: 600px; margin: 48px auto; }
    </style>
</head>
<body>
<div class="install-card">
    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div style="width:64px;height:64px;background:#1565C0;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:32px;color:white;margin-bottom:12px;">📅</div>
                <h4 class="fw-bold">AgendaJá — Instalação</h4>
                <p class="text-muted">Configuração inicial do sistema</p>
            </div>

            <?php foreach ($passos as [$tipo, $msg]): ?>
            <div class="alert alert-<?= $tipo === 'ok' ? 'success' : 'info' ?> d-flex align-items-center gap-2 py-2">
                <span><?= $tipo === 'ok' ? '✅' : 'ℹ️' ?></span>
                <span><?= $msg ?></span>
            </div>
            <?php endforeach; ?>

            <?php foreach ($erros as $erro): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
                <span>❌</span><span><?= htmlspecialchars($erro) ?></span>
            </div>
            <?php endforeach; ?>

            <?php if (empty($erros)): ?>
            <div class="alert alert-warning mt-3">
                <strong>⚠️ Importante:</strong> Delete ou renomeie o arquivo <code>install.php</code> após a instalação para proteger seu sistema.
            </div>
            <div class="d-grid mt-3">
                <a href="../admin/index.php" class="btn btn-primary btn-lg">
                    Ir para o Painel Admin →
                </a>
            </div>
            <?php else: ?>
            <div class="alert alert-danger mt-3">
                <strong>Instalação incompleta.</strong> Corrija os erros acima e tente novamente.
            </div>
            <?php endif; ?>
        </div>
    </div>
    <p class="text-center text-muted small mt-3">AgendaJá &copy; <?= date('Y') ?></p>
</div>
</body>
</html>
