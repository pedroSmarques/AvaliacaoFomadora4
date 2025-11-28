<?php
// php/admin/resumo.php
session_start();
require_once __DIR__ . "/../conexao.php";

header("Content-Type: application/json; charset=utf-8");

// Só admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

try {
    // Total de produtos
    $total_produtos = (int)$pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();

    // Total clientes
    $total_clientes = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE role = 'cliente'")->fetchColumn();

    // Total entregadores
    $total_entregadores = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE role = 'entregador'")->fetchColumn();

    // Total pedidos
    $total_pedidos = (int)$pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();

    echo json_encode([
        "ok" => true,
        "total_produtos"     => $total_produtos,
        "total_clientes"     => $total_clientes,
        "total_entregadores" => $total_entregadores,
        "total_pedidos"      => $total_pedidos
    ]);
} catch (Exception $e) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "Erro ao carregar resumo."
    ]);
}
