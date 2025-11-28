<?php
// php/admin/pedidos/listar_pedidos.php
session_start();
require_once __DIR__ . "/../../conexao.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

try {
    $sql = $pdo->query("
        SELECT 
            p.id,
            u.nome_completo AS cliente,
            p.endereco_entrega,
            p.numero,
            p.complemento,
            p.status,
            p.created_at
        FROM pedidos p
        INNER JOIN usuarios u ON u.id = p.cliente_id
        ORDER BY p.id DESC
        LIMIT 50
    ");

    $lista = [];
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $endereco = $row['endereco_entrega'];
        if (!empty($row['numero'])) {
            $endereco .= ', ' . $row['numero'];
        }
        if (!empty($row['complemento'])) {
            $endereco .= ' - ' . $row['complemento'];
        }

        $lista[] = [
            "id"        => (int)$row['id'],
            "cliente"   => $row['cliente'],
            "endereco"  => $endereco,
            "status"    => $row['status'],
            "created_at"=> $row['created_at']
        ];
    }

    echo json_encode([
        "ok"      => true,
        "pedidos" => $lista
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "Erro ao listar pedidos."
    ]);
}
