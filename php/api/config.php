<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'marc4901_agendaja');
define('DB_USER', 'marc4901_agendaja');
define('DB_PASS', 'Ma35881706@');
define('DB_CHARSET', 'utf8mb4');

define('JWT_SECRET', 'agendaja_secret_key_2024_mude_esta_chave');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ]);
        } catch (PDOException $e) {
            resposta(false, 'Erro de conexão com o banco de dados.', null, 500);
            exit();
        }
    }
    return $pdo;
}

function resposta($sucesso, $mensagem, $dados = null, $codigo = 200) {
    http_response_code($codigo);
    $res = ['sucesso' => $sucesso, 'mensagem' => $mensagem];
    if ($dados !== null) {
        $res = array_merge($res, $dados);
    }
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit();
}

function gerarToken($usuarioId) {
    $payload = base64_encode(json_encode([
        'id' => $usuarioId,
        'exp' => time() + (7 * 24 * 60 * 60),
    ]));
    $assinatura = hash_hmac('sha256', $payload, JWT_SECRET);
    return $payload . '.' . $assinatura;
}

function validarToken() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (empty($auth) || !str_starts_with($auth, 'Bearer ')) {
        resposta(false, 'Token de autenticação necessário.', null, 401);
    }
    $token = substr($auth, 7);
    $partes = explode('.', $token);
    if (count($partes) !== 2) {
        resposta(false, 'Token inválido.', null, 401);
    }
    [$payload, $assinatura] = $partes;
    $assinaturaEsperada = hash_hmac('sha256', $payload, JWT_SECRET);
    if (!hash_equals($assinaturaEsperada, $assinatura)) {
        resposta(false, 'Token inválido.', null, 401);
    }
    $dados = json_decode(base64_decode($payload), true);
    if ($dados['exp'] < time()) {
        resposta(false, 'Token expirado. Faça login novamente.', null, 401);
    }
    return $dados['id'];
}

function getBodyJson() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}
