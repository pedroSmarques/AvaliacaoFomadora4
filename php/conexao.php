<?php
// php/conexao.php
// Faz a conexão com o banco delivery_universitario usando PDO

$DB_HOST = '127.0.0.1';
$DB_NAME = 'delivery_universitario';
$DB_USER = 'root';
$DB_PASS = ''; // coloque sua senha do MySQL se tiver

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    die('Erro na conexão com o banco: ' . $e->getMessage());
}
