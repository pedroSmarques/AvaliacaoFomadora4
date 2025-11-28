<?php
// php/admin/produtos/salvar_produto.php
session_start();
require_once __DIR__ . "/../../conexao.php";

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$input = json_decode(file_get_contents("php://input"), true);

$id    = $input['id'] ?? "";
$nome  = trim($input['nome'] ?? "");
$preco = trim($input['preco'] ?? "");

if ($nome === "" || $preco === "") {
    echo json_encode(["ok" => false, "mensagem" => "Nome e preço são obrigatórios."]);
    exit;
}

$precoNum = floatval($preco);
if ($precoNum <= 0) {
    echo json_encode(["ok" => false, "mensagem" => "Preço inválido."]);
    exit;
}

try {
    if ($id !== "") {
        // Atualiza
        $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE id = ?");
        $stmt->execute([$nome, $precoNum, $id]);
        echo json_encode(["ok" => true, "mensagem" => "Produto atualizado com sucesso."]);
    } else {
        // Insere
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, ativo) VALUES (?, ?, 1)");
        $stmt->execute([$nome, $precoNum]);
        echo json_encode(["ok" => true, "mensagem" => "Produto cadastrado com sucesso."]);
    }
} catch (Exception $e) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "Erro ao salvar produto: " . $e->getMessage()
    ]);
}
