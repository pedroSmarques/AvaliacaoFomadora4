<?php
session_start();
require_once __DIR__ . "/../../conexao.php";

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$id    = $input['id'] ?? null;

if (!$id) {
    echo json_encode(["ok" => false, "mensagem" => "ID inválido."]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->execute([$id]);

    echo json_encode(["ok" => true, "mensagem" => "Usuário removido."]);

} catch (Exception $e) {
    echo json_encode(["ok" => false, "mensagem" => $e->getMessage()]);
}
