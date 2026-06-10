<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, 'Método não permitido.', null, 405);
}

$dados = getBodyJson();
$email = trim($dados['email'] ?? '');
$senha = $dados['senha'] ?? '';

if (empty($email) || empty($senha)) {
    resposta(false, 'E-mail e senha são obrigatórios.');
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT id, nome, email, senha, tipo_usuario, empresa_id, ativo FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario) {
    resposta(false, 'E-mail ou senha incorretos.');
}

if (!password_verify($senha, $usuario['senha'])) {
    resposta(false, 'E-mail ou senha incorretos.');
}

if (!$usuario['ativo']) {
    resposta(false, 'Conta desativada. Entre em contato com o suporte.');
}

$token = gerarToken($usuario['id']);

resposta(true, 'Login realizado com sucesso.', [
    'token' => $token,
    'usuario' => [
        'id' => (int)$usuario['id'],
        'nome' => $usuario['nome'],
        'email' => $usuario['email'],
        'tipo_usuario' => $usuario['tipo_usuario'],
        'empresa_id' => $usuario['empresa_id'] ? (int)$usuario['empresa_id'] : null,
        'token' => $token,
    ],
]);
