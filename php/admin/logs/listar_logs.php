<?php
session_start();
require_once __DIR__ . "/../../conexao.php";

header("Content-Type: application/json; charset=utf-8");

try {
    $stmt = $pdo->query("
        SELECT 
            l.id,
            u.nome_completo AS usuario,
            l.role,
            l.data_hora
        FROM logs_acesso l
        LEFT JOIN usuarios u ON u.id = l.usuario_id
        ORDER BY l.id DESC
    ");

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "ok"   => true,
        "logs" => $logs
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "ok"   => false,
        "mensagem" => "Erro ao listar logs: " . $e->getMessage()
    ]);
}
