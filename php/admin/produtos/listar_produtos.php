<?php
// php/admin/produtos/listar_produtos.php
session_start();
require_once __DIR__ . "/../../conexao.php";

// Garante que só admin acesse
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$id = $_GET['id'] ?? null;

try {
    if ($id) {
        // Busca um produto específico
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            echo json_encode(["ok" => false, "mensagem" => "Produto não encontrado."]);
            exit;
        }

        echo json_encode([
            "ok" => true,
            "produto" => $produto
        ]);
        exit;
    }

    // Lista todos os produtos
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "ok" => true,
        "produtos" => $produtos
    ]);
} catch (Exception $e) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "Erro ao listar produtos: " . $e->getMessage()
    ]);
}
