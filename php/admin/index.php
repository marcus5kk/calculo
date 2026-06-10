<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($login) || empty($senha)) {
        $erro = 'Preencha login e senha.';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, login, senha FROM admins WHERE login = ?");
        $stmt->execute([$login]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_login'] = $admin['login'];
            header('Location: dashboard.php');
            exit();
        } else {
            $erro = 'Login ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendaJá — Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0D47A1 0%, #1565C0 50%, #1976D2 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .brand { text-align: center; margin-bottom: 32px; }
        .brand-icon {
            width: 72px; height: 72px;
            background: #1565C0;
            border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 36px; color: white;
            margin-bottom: 12px;
        }
        .brand-name { font-size: 26px; font-weight: 700; color: #1565C0; }
        .brand-sub { font-size: 13px; color: #888; }
        .btn-primary { background: #1565C0; border-color: #1565C0; padding: 12px; font-size: 15px; font-weight: 600; border-radius: 10px; }
        .btn-primary:hover { background: #0D47A1; border-color: #0D47A1; }
        .form-control { border-radius: 10px; padding: 12px 16px; border: 1.5px solid #e0e0e0; }
        .form-control:focus { border-color: #1565C0; box-shadow: 0 0 0 3px rgba(21,101,192,0.12); }
        .form-label { font-weight: 600; font-size: 14px; color: #444; }
        .footer-text { text-align: center; margin-top: 24px; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="brand-name">AgendaJá</div>
        <div class="brand-sub">Painel Administrativo</div>
    </div>

    <?php if ($erro): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= htmlspecialchars($erro) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Login</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                <input type="text" name="login" class="form-control border-start-0"
                       placeholder="admin" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                       autocomplete="username" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Senha</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="senha" class="form-control border-start-0"
                       placeholder="••••••" autocomplete="current-password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar no Painel
        </button>
    </form>
    <div class="footer-text">AgendaJá &copy; <?= date('Y') ?></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
