<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'marc4901_agendaja');
define('DB_USER', 'marc4901_agendaja');
define('DB_PASS', 'Ma35881706');
define('DB_CHARSET', 'utf8mb4');

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
            die('<div style="background:#fee;padding:20px;color:red;font-family:sans-serif;">
                <strong>Erro de conexão com banco de dados.</strong><br>
                Verifique as configurações em includes/db.php<br>
                <small>' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }
    return $pdo;
}
