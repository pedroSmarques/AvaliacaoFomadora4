<?php
// php/admin/produtos/deletar_produto.php
session_start();
require_once __DIR__ . "/../../conexao.php";

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$input = json_decode(file_get_contents("php://input"), true);
$id    = $input['id'] ?? "";

if ($id === "") {
    echo json_encode(["ok" => false, "mensagem" => "ID não informado."]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["ok" => true, "mensagem" => "Produto excluído."]);
} catch (Exception $e) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "Erro ao excluir produto: " . $e->getMessage()
    ]);
}
