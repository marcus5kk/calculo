<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(false, 'Método não permitido.', null, 405);
}

$dados = getBodyJson();
$nome  = trim($dados['nome'] ?? '');
$email = trim(strtolower($dados['email'] ?? ''));
$senha = $dados['senha'] ?? '';

if (empty($nome) || empty($email) || empty($senha)) {
    resposta(false, 'Nome, e-mail e senha são obrigatórios.');
}

if (strlen($nome) < 3) {
    resposta(false, 'Nome muito curto.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    resposta(false, 'E-mail inválido.');
}

if (strlen($senha) < 6) {
    resposta(false, 'A senha deve ter pelo menos 6 caracteres.');
}

$pdo = getDB();

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    resposta(false, 'Este e-mail já está cadastrado.');
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo)
    VALUES (?, ?, ?, 'usuario_final', 1)
");
$stmt->execute([$nome, $email, $senhaHash]);

resposta(true, 'Cadastro realizado com sucesso! Faça login para continuar.');
