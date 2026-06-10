<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    resposta(false, 'Método não permitido.', null, 405);
}

$empresaUserId = validarToken();

$pdo = getDB();
$stmt = $pdo->prepare("SELECT tipo_usuario FROM usuarios WHERE id = ? AND ativo = 1");
$stmt->execute([$empresaUserId]);
$empresa = $stmt->fetch();

if (!$empresa || $empresa['tipo_usuario'] !== 'estabelecimento') {
    resposta(false, 'Acesso negado.', null, 403);
}

$empresaId = (int)($_GET['empresa_id'] ?? 0);

if ($empresaId !== (int)$empresaUserId) {
    resposta(false, 'Acesso negado.', null, 403);
}

$stmt = $pdo->prepare("
    SELECT id, nome, email, ativo, criado_em
    FROM usuarios
    WHERE tipo_usuario = 'funcionario' AND empresa_id = ? AND ativo = 1
    ORDER BY nome ASC
");
$stmt->execute([$empresaId]);
$funcionarios = $stmt->fetchAll();

foreach ($funcionarios as &$f) {
    $f['id'] = (int)$f['id'];
}

resposta(true, 'Funcionários carregados.', ['funcionarios' => $funcionarios]);
