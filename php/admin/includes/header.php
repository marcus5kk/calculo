<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgendaJá — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1565C0;
            --primary-dark: #0D47A1;
            --sidebar-width: 250px;
        }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--primary-dark);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            padding-top: 0;
        }
        .sidebar-brand {
            background: var(--primary);
            padding: 20px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand .icon { font-size: 28px; }
        .sidebar-brand .name { font-size: 20px; font-weight: 700; }
        .sidebar-brand .sub { font-size: 11px; opacity: 0.7; display: block; }
        .sidebar-nav { padding: 12px 0; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 10px;
            color: rgba(255,255,255,0.75);
            padding: 10px 20px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #42A5F5;
        }
        .sidebar-nav a i { font-size: 18px; width: 22px; text-align: center; }
        .sidebar-nav .nav-section {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.4);
            padding: 12px 20px 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: white;
            padding: 14px 24px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 99;
        }
        .page-title { font-size: 18px; font-weight: 600; color: #333; margin: 0; }
        .content-area { padding: 24px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); }
        .stat-card {
            border-radius: 12px; padding: 20px;
            display: flex; align-items: center; gap: 16px;
            color: white; border: none;
        }
        .stat-card .stat-icon {
            font-size: 32px; opacity: 0.85;
        }
        .stat-card .stat-value { font-size: 28px; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: 13px; opacity: 0.85; }
        .badge-tipo {
            padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .badge-estabelecimento { background: #e8f5e9; color: #2E7D32; }
        .badge-funcionario { background: #f3e5f5; color: #6A1B9A; }
        .badge-usuario_final { background: #e3f2fd; color: #1565C0; }
    </style>
</head>
<body>
<div class="sidebar">
    <a href="dashboard.php" class="sidebar-brand">
        <i class="bi bi-calendar-check icon"></i>
        <div>
            <span class="name">AgendaJá</span>
            <span class="sub">Painel Administrativo</span>
        </div>
    </a>
    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="nav-section">Usuários</div>
        <a href="estabelecimentos.php" class="<?= basename($_SERVER['PHP_SELF']) === 'estabelecimentos.php' ? 'active' : '' ?>">
            <i class="bi bi-shop"></i> Estabelecimentos
        </a>
        <a href="usuarios.php" class="<?= basename($_SERVER['PHP_SELF']) === 'usuarios.php' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Usuários Finais
        </a>
        <div class="nav-section">Sistema</div>
        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i> Sair
        </a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><i class="bi bi-person-circle me-1"></i>Administrador</span>
        </div>
    </div>
    <div class="content-area">
