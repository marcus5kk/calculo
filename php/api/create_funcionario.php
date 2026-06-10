<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, 'Método não permitido.', null, 405);
}

$empresaUserId = validarToken();

$pdo = getDB();
$stmt = $pdo->prepare("SELECT tipo_usuario FROM usuarios WHERE id = ? AND ativo = 1");
$stmt->execute([$empresaUserId]);
$empresa = $stmt->fetch();

if (!$empresa || $empresa['tipo_usuario'] !== 'estabelecimento') {
    resposta(false, 'Acesso negado. Apenas estabelecimentos podem criar funcionários.', null, 403);
}

$dados    = getBodyJson();
$nome     = trim($dados['nome'] ?? '');
$email    = trim(strtolower($dados['email'] ?? ''));
$senha    = $dados['senha'] ?? '';
$empresaId = (int)($dados['empresa_id'] ?? 0);

if (empty($nome) || empty($email) || empty($senha) || $empresaId === 0) {
    resposta(false, 'Nome, e-mail, senha e empresa são obrigatórios.');
}

if ($empresaId !== (int)$empresaUserId) {
    resposta(false, 'Acesso negado.', null, 403);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    resposta(false, 'E-mail inválido.');
}

if (strlen($senha) < 6) {
    resposta(false, 'A senha deve ter pelo menos 6 caracteres.');
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    resposta(false, 'Este e-mail já está cadastrado.');
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO usuarios (nome, email, senha, tipo_usuario, empresa_id, ativo)
    VALUES (?, ?, ?, 'funcionario', ?, 1)
");
$stmt->execute([$nome, $email, $senhaHash, $empresaId]);
$novoId = $pdo->lastInsertId();

resposta(true, 'Funcionário cadastrado com sucesso!', [
    'funcionario' => [
        'id' => (int)$novoId,
        'nome' => $nome,
        'email' => $email,
        'tipo_usuario' => 'funcionario',
        'empresa_id' => $empresaId,
    ],
]);
